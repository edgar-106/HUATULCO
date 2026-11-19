using System.ComponentModel.DataAnnotations;

namespace HuatulcoTravel.Models;

public class ComprobantePago
{
    public Guid Id { get; set; }
    public Guid PagoId { get; set; }
    public Pago? Pago { get; set; }

    [Required, StringLength(500)]
    public string RutaPrivada { get; set; } = string.Empty;

    [Required, StringLength(120)]
    public string NombreArchivoOriginal { get; set; } = string.Empty;

    [Required, StringLength(100)]
    public string TipoContenido { get; set; } = string.Empty;

    public long TamanoBytes { get; set; }
    public DateTime FechaCargaUtc { get; set; } = DateTime.UtcNow;
}
