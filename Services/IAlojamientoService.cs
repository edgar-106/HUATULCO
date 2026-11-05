using HuatulcoTravel.Models;

namespace HuatulcoTravel.Services;

public interface IAlojamientoService
{
    Task<List<Alojamiento>> ObtenerTodosAsync(bool soloPublicados = false);
    Task<Alojamiento?> ObtenerPorIdAsync(Guid id);
    Task CrearAsync(Alojamiento alojamiento);
    Task ActualizarAsync(Alojamiento alojamiento);
    Task<bool> EliminarAsync(Guid id);
}
