using Microsoft.AspNetCore.Identity;

namespace HuatulcoTravel.Models;

public class ApplicationUser : IdentityUser<Guid>
{
    public string Nombre { get; set; } = string.Empty;
    public string? Apellidos { get; set; }
    public DateTime FechaRegistroUtc { get; set; } = DateTime.UtcNow;
    public bool Activo { get; set; } = true;
}
