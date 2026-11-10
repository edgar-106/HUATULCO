using HuatulcoTravel.Models;
using Microsoft.AspNetCore.Identity;
using Microsoft.EntityFrameworkCore;

namespace HuatulcoTravel.Data;

public static class SeedData
{
    public static async Task InitializeAsync(IServiceProvider services, IConfiguration config)
    {
        using var scope = services.CreateScope();
        var roles = scope.ServiceProvider.GetRequiredService<RoleManager<IdentityRole<Guid>>>();
        var users = scope.ServiceProvider.GetRequiredService<UserManager<ApplicationUser>>();
        var db = scope.ServiceProvider.GetRequiredService<ApplicationDbContext>();

        foreach (var role in new[] { "Administrador", "Cliente", "Operativo", "Proveedor" })
        {
            if (!await roles.RoleExistsAsync(role))
            {
                var r = await roles.CreateAsync(new IdentityRole<Guid>
                {
                    Id = Guid.NewGuid(),
                    Name = role,
                    NormalizedName = role.ToUpperInvariant()
                });

                if (!r.Succeeded)
                    throw new InvalidOperationException(string.Join(";", r.Errors.Select(x => x.Description)));
            }
        }

        var email = (config["BootstrapAdmin:Email"] ?? "admin@huatulcotravel.com").Trim();
        var password = config["BootstrapAdmin:Password"] ?? "Admin1234#*";
        var name = config["BootstrapAdmin:Name"] ?? "Administrador Huatulco";

        var admin = await users.FindByEmailAsync(email);
        if (admin == null)
        {
            admin = new ApplicationUser
            {
                Id = Guid.NewGuid(),
                UserName = email,
                Email = email,
                Nombre = name,
                FechaRegistroUtc = DateTime.UtcNow,
                Activo = true
            };

            var c = await users.CreateAsync(admin, password);
            if (!c.Succeeded)
                throw new InvalidOperationException(string.Join(";", c.Errors.Select(x => x.Description)));
        }
        else
        {
            admin.Activo = true;
            await users.UpdateAsync(admin);

            // Restablecer contraseña para asegurar acceso garantizado con Admin1234#*
            var token = await users.GeneratePasswordResetTokenAsync(admin);
            await users.ResetPasswordAsync(admin, token, password);
        }

        if (!await users.IsInRoleAsync(admin, "Administrador"))
        {
            await users.AddToRoleAsync(admin, "Administrador");
        }

        // Asegurar que ÚNICAMENTE admin@huatulcotravel.com sea Administrador; los demás quedan como Cliente
        var allAdmins = await users.GetUsersInRoleAsync("Administrador");
        foreach (var otherAdmin in allAdmins)
        {
            if (!string.Equals(otherAdmin.Email, email, StringComparison.OrdinalIgnoreCase))
            {
                await users.RemoveFromRoleAsync(otherAdmin, "Administrador");
                if (!await users.IsInRoleAsync(otherAdmin, "Cliente"))
                {
                    await users.AddToRoleAsync(otherAdmin, "Cliente");
                }
            }
        }

        // Sembrar alojamientos de muestra si la base de datos no tiene
        if (!await db.Alojamientos.AnyAsync())
        {
            db.Alojamientos.AddRange(
                new Alojamiento
                {
                    Id = Guid.NewGuid(),
                    Nombre = "Villa Paraíso Tangolunda",
                    Descripcion = "Espectacular villa contemporánea con vista panorámica al océano Pacífico, alberca privada tipo infinity, terraza para asados y acceso exclusivo a la playa de Tangolunda.",
                    Tipo = TipoAlojamiento.Casa,
                    CapacidadHuespedes = 8,
                    NumeroHabitaciones = 4,
                    NumeroBanos = 5,
                    PrecioBasePorNoche = 5200.00m,
                    Direccion = "Paseo de los Cocoteros Lote 14, Bahía Tangolunda",
                    Latitud = 15.7674,
                    Longitud = -96.0963,
                    ImagenPrincipalUrl = "https://images.unsplash.com/photo-1580587771525-78b9dba3b914?auto=format&fit=crop&w=1200&q=80",
                    Estado = EstadoAlojamiento.Publicado,
                    Activo = true,
                    FechaCreacionUtc = DateTime.UtcNow
                },
                new Alojamiento
                {
                    Id = Guid.NewGuid(),
                    Nombre = "Condominio Vista Mar Chahué",
                    Descripcion = "Cómodo y luminoso departamento a 3 minutos caminando del Club de Playa Chahué. Cuenta con balcón con vista a la marina, cocina integral equipada y alberca común.",
                    Tipo = TipoAlojamiento.Departamento,
                    CapacidadHuespedes = 4,
                    NumeroHabitaciones = 2,
                    NumeroBanos = 2,
                    PrecioBasePorNoche = 2450.00m,
                    Direccion = "Blvd. Benito Juárez 102, Marina Chahué",
                    Latitud = 15.7601,
                    Longitud = -96.1264,
                    ImagenPrincipalUrl = "https://images.unsplash.com/photo-1522708323590-d24dbb6b0267?auto=format&fit=crop&w=1200&q=80",
                    Estado = EstadoAlojamiento.Publicado,
                    Activo = true,
                    FechaCreacionUtc = DateTime.UtcNow
                },
                new Alojamiento
                {
                    Id = Guid.NewGuid(),
                    Nombre = "Residencia Península Conejos",
                    Descripcion = "Exclusiva residencia sobre el acantilado de Bahía Conejos. Arquitectura abierta con brisa marina constante, acabados en parota, alberca privada y servicio de concierge.",
                    Tipo = TipoAlojamiento.Casa,
                    CapacidadHuespedes = 10,
                    NumeroHabitaciones = 5,
                    NumeroBanos = 6,
                    PrecioBasePorNoche = 7800.00m,
                    Direccion = "Fraccionamiento Punta Conejos Lote 8, Bahías de Huatulco",
                    Latitud = 15.7720,
                    Longitud = -96.0715,
                    ImagenPrincipalUrl = "https://images.unsplash.com/photo-1613490493576-7fde63acd811?auto=format&fit=crop&w=1200&q=80",
                    Estado = EstadoAlojamiento.Publicado,
                    Activo = true,
                    FechaCreacionUtc = DateTime.UtcNow
                },
                new Alojamiento
                {
                    Id = Guid.NewGuid(),
                    Nombre = "Suite Boutique Marina Santa Cruz",
                    Descripcion = "Suite romántica ideal para parejas, a unos pasos del embarcadero de Santa Cruz y mercado de artesanías. Incluye cama King size, terraza privada y desayuno continental.",
                    Tipo = TipoAlojamiento.Habitacion,
                    CapacidadHuespedes = 2,
                    NumeroHabitaciones = 1,
                    NumeroBanos = 1,
                    PrecioBasePorNoche = 1600.00m,
                    Direccion = "Calle Mitla s/n, Bahía Santa Cruz",
                    Latitud = 15.7538,
                    Longitud = -96.1332,
                    ImagenPrincipalUrl = "https://images.unsplash.com/photo-1590490360182-c33d57733427?auto=format&fit=crop&w=1200&q=80",
                    Estado = EstadoAlojamiento.Publicado,
                    Activo = true,
                    FechaCreacionUtc = DateTime.UtcNow
                },
                new Alojamiento
                {
                    Id = Guid.NewGuid(),
                    Nombre = "Eco-Lodge Cabaña La Bocana",
                    Descripcion = "Refugio sustentable rodeado de vegetación tropical donde el río Copalita se une con el mar. Ideal para amantes del surf, avistamiento de aves y noches estrelladas.",
                    Tipo = TipoAlojamiento.Casa,
                    CapacidadHuespedes = 4,
                    NumeroHabitaciones = 2,
                    NumeroBanos = 2,
                    PrecioBasePorNoche = 1950.00m,
                    Direccion = "Camino a la Bocana del Río Copalita, Huatulco",
                    Latitud = 15.7761,
                    Longitud = -96.0652,
                    ImagenPrincipalUrl = "https://images.unsplash.com/photo-1518780664697-55e3ad937233?auto=format&fit=crop&w=1200&q=80",
                    Estado = EstadoAlojamiento.Publicado,
                    Activo = true,
                    FechaCreacionUtc = DateTime.UtcNow
                },
                new Alojamiento
                {
                    Id = Guid.NewGuid(),
                    Nombre = "Loft Moderno Bahía Maguey",
                    Descripcion = "Estudio de diseño abierto en una de las zonas más tranquilas de Huatulco. Grandes ventanales con vista a la bahía de aguas cristalinas ideales para esnórquel.",
                    Tipo = TipoAlojamiento.Apartamento,
                    CapacidadHuespedes = 3,
                    NumeroHabitaciones = 1,
                    NumeroBanos = 1,
                    PrecioBasePorNoche = 1850.00m,
                    Direccion = "Mirador Bahía Maguey s/n, Huatulco",
                    Latitud = 15.7485,
                    Longitud = -96.1512,
                    ImagenPrincipalUrl = "https://images.unsplash.com/photo-1502672260266-1c1ef2d93688?auto=format&fit=crop&w=1200&q=80",
                    Estado = EstadoAlojamiento.Publicado,
                    Activo = true,
                    FechaCreacionUtc = DateTime.UtcNow
                },
                new Alojamiento
                {
                    Id = Guid.NewGuid(),
                    Nombre = "Villa Arrocito con Acceso a Caleta",
                    Descripcion = "Hermosa casa vacacional a 100 metros de la apacible caleta de Playa Arrocito. Jardín privado con palmeras, alberca y palapa para descansar.",
                    Tipo = TipoAlojamiento.Casa,
                    CapacidadHuespedes = 6,
                    NumeroHabitaciones = 3,
                    NumeroBanos = 3,
                    PrecioBasePorNoche = 3900.00m,
                    Direccion = "Fracc. Residencial Arrocito, Huatulco",
                    Latitud = 15.7612,
                    Longitud = -96.1105,
                    ImagenPrincipalUrl = "https://images.unsplash.com/photo-1512917774080-9991f1c4c750?auto=format&fit=crop&w=1200&q=80",
                    Estado = EstadoAlojamiento.Publicado,
                    Activo = true,
                    FechaCreacionUtc = DateTime.UtcNow
                },
                new Alojamiento
                {
                    Id = Guid.NewGuid(),
                    Nombre = "Penthouse Panorámico Bahía Chahué",
                    Descripcion = "Espectacular penthouse de dos niveles con jacuzzi privado en la terraza superior, vista 360° a los atardeceres de Huatulco y acabados de mármol.",
                    Tipo = TipoAlojamiento.Departamento,
                    CapacidadHuespedes = 6,
                    NumeroHabitaciones = 3,
                    NumeroBanos = 3,
                    PrecioBasePorNoche = 4100.00m,
                    Direccion = "Av. Carrizal Lote 5, Bahía Chahué",
                    Latitud = 15.7635,
                    Longitud = -96.1240,
                    ImagenPrincipalUrl = "https://images.unsplash.com/photo-1560448204-e02f11c3d0e2?auto=format&fit=crop&w=1200&q=80",
                    Estado = EstadoAlojamiento.Publicado,
                    Activo = true,
                    FechaCreacionUtc = DateTime.UtcNow
                }
            );

            await db.SaveChangesAsync();
        }
    }
}
