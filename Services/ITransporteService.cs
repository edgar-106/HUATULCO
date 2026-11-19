using HuatulcoTravel.Models;

namespace HuatulcoTravel.Services;

public interface ITransporteService
{
    Task<List<Transporte>> ListarAsync(Guid? reservacionId = null);
    Task<Transporte?> ObtenerPorIdAsync(Guid id);
    Task<bool> CrearAsync(Transporte transporte);
    Task<bool> ActualizarAsync(Transporte transporte);
    Task<bool> EliminarAsync(Guid id);
}
