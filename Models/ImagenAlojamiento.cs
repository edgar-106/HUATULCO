using System.ComponentModel.DataAnnotations;

namespace HuatulcoTravel.Models;

public class ImagenAlojamiento
{
    public Guid Id { get; set; }
    public Guid AlojamientoId { get; set; }
    public Alojamiento? Alojamiento { get; set; }

    [Required, StringLength(500)]
    public string RutaArchivo { get; set; } = string.Empty;

    [StringLength(180)]
    public string? TextoAlternativo { get; set; }

    public bool EsPrincipal { get; set; }
    public int Orden { get; set; }
}
