using HuatulcoTravel.Models;
using System.ComponentModel.DataAnnotations;

namespace HuatulcoTravel.ViewModels;

public class AlojamientoFormViewModel
{
    public Guid? Id { get; set; }

    [Required, StringLength(120)]
    [Display(Name = "Nombre del alojamiento")]
    public string Nombre { get; set; } = string.Empty;

    [Required, StringLength(3000)]
    public string Descripcion { get; set; } = string.Empty;

    [Display(Name = "Tipo de alojamiento")]
    public TipoAlojamiento Tipo { get; set; }

    [Range(1, 100)]
    [Display(Name = "Capacidad de huéspedes")]
    public int CapacidadHuespedes { get; set; } = 2;

    [Range(0, 50)]
    [Display(Name = "Número de habitaciones")]
    public int NumeroHabitaciones { get; set; }

    [Range(0, 50)]
    [Display(Name = "Número de baños")]
    public int NumeroBanos { get; set; }

    [Range(typeof(decimal), "0.01", "999999999")]
    [Display(Name = "Precio base por noche")]
    public decimal PrecioBasePorNoche { get; set; }

    [Display(Name = "Dirección")]
    public string Direccion { get; set; } = string.Empty;

    [Range(-90, 90)]
    public double? Latitud { get; set; }

    [Range(-180, 180)]
    public double? Longitud { get; set; }

    [Display(Name = "Estado")]
    public EstadoAlojamiento Estado { get; set; } = EstadoAlojamiento.Borrador;
}
