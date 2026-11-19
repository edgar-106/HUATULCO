using System.ComponentModel.DataAnnotations;
using System.ComponentModel.DataAnnotations.Schema;

namespace HuatulcoTravel.Models;

public class Alojamiento
{
    public Guid Id { get; set; }

    [Required, StringLength(120)]
    public string Nombre { get; set; } = string.Empty;

    [Required, StringLength(3000)]
    public string Descripcion { get; set; } = string.Empty;

    public TipoAlojamiento Tipo { get; set; }

    [Range(1, 100)]
    public int CapacidadHuespedes { get; set; }

    [Range(0, 50)]
    public int NumeroHabitaciones { get; set; }

    [Range(0, 50)]
    public int NumeroBanos { get; set; }

    [Column(TypeName = "numeric(12,2)")]
    [Range(typeof(decimal), "0.01", "999999999")]
    public decimal PrecioBasePorNoche { get; set; }

    [StringLength(250)]
    public string Direccion { get; set; } = string.Empty;

    [Range(-90, 90)]
    public double? Latitud { get; set; }

    [Range(-180, 180)]
    public double? Longitud { get; set; }

    [StringLength(500)]
    public string? ImagenPrincipalUrl { get; set; }

    public EstadoAlojamiento Estado { get; set; } = EstadoAlojamiento.Borrador;
    public bool Activo { get; set; } = true; // Baja lógica.

    public DateTime FechaCreacionUtc { get; set; } = DateTime.UtcNow;
    public DateTime? FechaActualizacionUtc { get; set; }

    public ICollection<ImagenAlojamiento> Imagenes { get; set; } = new List<ImagenAlojamiento>();
}
