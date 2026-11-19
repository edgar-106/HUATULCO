using HuatulcoTravel.Models;

namespace HuatulcoTravel.Services;

public interface IPagoService
{
    Task<List<Pago>> ListarPendientesRevisionAsync();
    Task<Pago?> ObtenerPorIdAsync(Guid id);
    Task<bool> AprobarAsync(Guid id, Guid adminId);
    Task<bool> RechazarAsync(Guid id, Guid adminId);
}
