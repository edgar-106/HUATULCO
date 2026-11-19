using HuatulcoTravel.Models;

namespace HuatulcoTravel.Services;

public interface IReservacionService
{
    Task<List<Reservacion>> ListarAsync(Guid? alojamientoId = null, EstadoReservacion? estado = null);
    Task<Reservacion?> ObtenerPorIdAsync(Guid id);
    Task<bool> CancelarAsync(Guid id);
    Task<bool> ConfirmarAsync(Guid id);
}
