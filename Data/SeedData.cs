using HuatulcoTravel.Models;
using Microsoft.AspNetCore.Identity;

namespace HuatulcoTravel.Data;

public static class SeedData
{
    public static async Task InitializeAsync(IServiceProvider services, IConfiguration config)
    {
        using var scope = services.CreateScope();
        var roles = scope.ServiceProvider.GetRequiredService<RoleManager<IdentityRole<Guid>>>();
        var users = scope.ServiceProvider.GetRequiredService<UserManager<ApplicationUser>>();

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

        var email = config["BootstrapAdmin:Email"] ?? "admin@huatulcotravel.com";
        var password = config["BootstrapAdmin:Password"] ?? "Admin1234#*";
        var name = config["BootstrapAdmin:Name"] ?? "Administrador Huatulco";

        if (!string.IsNullOrWhiteSpace(email) && !string.IsNullOrWhiteSpace(password) && (await users.GetUsersInRoleAsync("Administrador")).Count == 0)
        {
            var admin = await users.FindByEmailAsync(email);
            if (admin == null)
            {
                admin = new ApplicationUser
                {
                    Id = Guid.NewGuid(),
                    UserName = email.Trim(),
                    Email = email.Trim(),
                    Nombre = name,
                    FechaRegistroUtc = DateTime.UtcNow,
                    Activo = true
                };

                var c = await users.CreateAsync(admin, password);
                if (!c.Succeeded)
                    throw new InvalidOperationException(string.Join(";", c.Errors.Select(x => x.Description)));
            }

            var a = await users.AddToRoleAsync(admin, "Administrador");
            if (!a.Succeeded)
                throw new InvalidOperationException(string.Join(";", a.Errors.Select(x => x.Description)));
        }
    }
}
