using System.ComponentModel.DataAnnotations;

namespace HuatulcoTravel.Models;

public class Transporte
{
    public Guid Id { get; set; }

    public Guid ReservacionId { get; set; }
    public Reservacion? Reservacion { get; set; }

    [Required, StringLength(150)]
    public string Motivo { get; set; } = string.Empty;

    [Required, StringLength(60)]
    public string VehiculoTipo { get; set; } = string.Empty;

    [StringLength(20)]
    public string? Placas { get; set; }

    [StringLength(120)]
    public string? Conductor { get; set; }

    public DateOnly? FechaRecogida { get; set; }
    public DateOnly? FechaDevolucion { get; set; }

    public string? Notas { get; set; }

    public EstadoTransporte Estado { get; set; } = EstadoTransporte.Asignado;

    public DateTime FechaCreacionUtc { get; set; } = DateTime.UtcNow;
}
