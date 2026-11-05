using System.ComponentModel.DataAnnotations;
using System.ComponentModel.DataAnnotations.Schema;

namespace HuatulcoTravel.Models;

public class Reservacion
{
    public Guid Id { get; set; }

    [Required, StringLength(30)]
    public string Folio { get; set; } = string.Empty;

    public Guid AlojamientoId { get; set; }
    public Alojamiento? Alojamiento { get; set; }

    // Es nulo cuando el cliente reserva como invitado.
    public Guid? UsuarioId { get; set; }
    public ApplicationUser? Usuario { get; set; }

    [Required, StringLength(120)]
    public string NombreHuesped { get; set; } = string.Empty;

    [Required, EmailAddress, StringLength(180)]
    public string EmailHuesped { get; set; } = string.Empty;

    [Required, Phone, StringLength(30)]
    public string TelefonoHuesped { get; set; } = string.Empty;

    public DateOnly FechaEntrada { get; set; }
    public DateOnly FechaSalida { get; set; }

    [Range(1, 100)]
    public int NumeroHuespedes { get; set; }

    public bool IncluyeLimpieza { get; set; }

    [Column(TypeName = "numeric(12,2)")]
    public decimal Subtotal { get; set; }

    [Column(TypeName = "numeric(12,2)")]
    public decimal CargoLimpieza { get; set; }

    [Column(TypeName = "numeric(12,2)")]
    public decimal Descuento { get; set; }

    [Column(TypeName = "numeric(12,2)")]
    public decimal Impuestos { get; set; }

    [Column(TypeName = "numeric(12,2)")]
    public decimal Total { get; set; }

    public EstadoReservacion Estado { get; set; } = EstadoReservacion.PendienteDePago;

    public DateTime FechaCreacionUtc { get; set; } = DateTime.UtcNow;

    [Timestamp]
    public uint Version { get; set; }
}
