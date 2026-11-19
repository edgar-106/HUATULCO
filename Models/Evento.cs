using System.ComponentModel.DataAnnotations;

namespace HuatulcoTravel.Models;

public class Evento
{
    public Guid Id { get; set; }

    public Guid AlojamientoId { get; set; }
    public Alojamiento? Alojamiento { get; set; }

    public Guid? UsuarioId { get; set; }
    public ApplicationUser? Usuario { get; set; }

    public Guid? ReservacionId { get; set; }
    public Reservacion? Reservacion { get; set; }

    [Required, StringLength(120)]
    public string Nombre { get; set; } = string.Empty;

    public TipoEvento Tipo { get; set; }

    public bool EsSporadico { get; set; }

    public DateOnly Fecha { get; set; }

    [StringLength(300)]
    public string? Descripcion { get; set; }

    [StringLength(150)]
    public string? CentroAtractivo { get; set; }

    [StringLength(150)]
    public string? Proveedor { get; set; }

    public EstadoEvento Estado { get; set; } = EstadoEvento.Programada;

    public DateTime FechaCreacionUtc { get; set; } = DateTime.UtcNow;

    public DateTime? FechaActualizacionUtc { get; set; }
}
