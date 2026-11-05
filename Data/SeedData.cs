using HuatulcoTravel.Models;
using Microsoft.AspNetCore.Identity;
using Microsoft.EntityFrameworkCore;

namespace HuatulcoTravel.Data;

public static class SeedData
{
    public static async Task InitializeAsync(IServiceProvider services)
    {
        using var scope = services.CreateScope();
        var roleManager = scope.ServiceProvider.GetRequiredService<RoleManager<IdentityRole<Guid>>>();
        var userManager = scope.ServiceProvider.GetRequiredService<UserManager<ApplicationUser>>();
        var db = scope.ServiceProvider.GetRequiredService<ApplicationDbContext>();

        string[] roles = ["Administrador", "Cliente", "Operativo", "Proveedor"];

        foreach (var role in roles)
        {
            if (!await roleManager.RoleExistsAsync(role))
            {
                var result = await roleManager.CreateAsync(new IdentityRole<Guid>
                {
                    Id = Guid.NewGuid(),
                    Name = role,
                    NormalizedName = role.ToUpperInvariant()
                });

                if (!result.Succeeded)
                    throw new InvalidOperationException(
                        $"No se pudo crear el rol {role}: {string.Join(", ", result.Errors.Select(e => e.Description))}");
            }
        }

        // Crear administrador inicial si no existe
        const string adminEmail = "admin@huatulcotravel.com";
        var admin = await userManager.FindByEmailAsync(adminEmail);
        if (admin is null)
        {
            admin = new ApplicationUser
            {
                Id = Guid.NewGuid(),
                UserName = adminEmail,
                Email = adminEmail,
                Nombre = "Administrador",
                Apellidos = "Huatulco",
                EmailConfirmed = true,
                Activo = true
            };

            var userResult = await userManager.CreateAsync(admin, "Admin1234#*");
            if (userResult.Succeeded)
            {
                await userManager.AddToRoleAsync(admin, "Administrador");
            }
        }

        // Sembrar alojamientos de muestra si la base de datos está vacía
        if (!await db.Alojamientos.AnyAsync())
        {
            db.Alojamientos.AddRange(
                new Alojamiento
                {
                    Id = Guid.NewGuid(),
                    Nombre = "Villa Paraíso Tangolunda",
                    Descripcion = "Hermosa casa privada con alberca, vista panorámica al océano y acabados de lujo.",
                    Tipo = TipoAlojamiento.Casa,
                    CapacidadHuespedes = 8,
                    NumeroHabitaciones = 4,
                    NumeroBanos = 4,
                    PrecioBasePorNoche = 4500.00m,
                    Direccion = "Bahía Tangolunda Lote 12, Huatulco",
                    Latitud = 15.7674,
                    Longitud = -96.0963,
                    Estado = EstadoAlojamiento.Publicado,
                    FechaCreacionUtc = DateTime.UtcNow
                },
                new Alojamiento
                {
                    Id = Guid.NewGuid(),
                    Nombre = "Condominio Frente al Mar en Chahué",
                    Descripcion = "Departamento moderno a pasos del club de playa y marina de Chahué, totalmente equipado.",
                    Tipo = TipoAlojamiento.Departamento,
                    CapacidadHuespedes = 4,
                    NumeroHabitaciones = 2,
                    NumeroBanos = 2,
                    PrecioBasePorNoche = 2200.00m,
                    Direccion = "Blvd. Chahué s/n, Bahías de Huatulco",
                    Latitud = 15.7601,
                    Longitud = -96.1264,
                    Estado = EstadoAlojamiento.Publicado,
                    FechaCreacionUtc = DateTime.UtcNow
                },
                new Alojamiento
                {
                    Id = Guid.NewGuid(),
                    Nombre = "Suite Marina Chahué",
                    Descripcion = "Habitación acogedora y elegante con balcón privado y vista a la bahía.",
                    Tipo = TipoAlojamiento.Habitacion,
                    CapacidadHuespedes = 2,
                    NumeroHabitaciones = 1,
                    NumeroBanos = 1,
                    PrecioBasePorNoche = 1200.00m,
                    Direccion = "Marina Chahué, Huatulco",
                    Latitud = 15.7625,
                    Longitud = -96.1280,
                    Estado = EstadoAlojamiento.Publicado,
                    FechaCreacionUtc = DateTime.UtcNow
                }
            );

            await db.SaveChangesAsync();
        }
    }
}
