using HuatulcoTravel.Models;

namespace HuatulcoTravel.Services;

public interface IEventoService
{
    Task<List<Evento>> ListarAsync(Guid? alojamientoId = null, TipoEvento? tipo = null, bool? esSporadico = null, EstadoEvento? estado = null);
    Task<List<Evento>> ListarPorAlojamientoAsync(Guid alojamientoId);
    Task<Evento?> ObtenerPorIdAsync(Guid id);
    Task<bool> CrearAsync(Evento evento);
    Task<bool> ActualizarAsync(Evento evento);
    Task<bool> EliminarAsync(Guid id);
}
