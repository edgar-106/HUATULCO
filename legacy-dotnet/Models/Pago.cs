using System.ComponentModel.DataAnnotations;
using System.ComponentModel.DataAnnotations.Schema;

namespace HuatulcoTravel.Models;

public enum EstadoPago
{
    Pendiente = 1,
    EnRevision = 2,
    Aprobado = 3,
    Rechazado = 4,
    Reembolsado = 5
}

public class Pago
{
    public Guid Id { get; set; }
    public Guid ReservacionId { get; set; }
    public Reservacion? Reservacion { get; set; }

    [Required, StringLength(40)]
    public string Metodo { get; set; } = "Transferencia";

    [Column(TypeName = "numeric(12,2)")]
    public decimal Importe { get; set; }

    public EstadoPago Estado { get; set; } = EstadoPago.Pendiente;
    public DateTime FechaRegistroUtc { get; set; } = DateTime.UtcNow;
    public DateTime? FechaRevisionUtc { get; set; }
    public Guid? RevisadoPorId { get; set; }

    public ICollection<ComprobantePago> Comprobantes { get; set; } = new List<ComprobantePago>();
}
