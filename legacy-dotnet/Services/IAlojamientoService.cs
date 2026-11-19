using HuatulcoTravel.Models; namespace HuatulcoTravel.Services;
public interface IAlojamientoService { Task<List<Alojamiento>> ObtenerTodosAsync(bool soloPublicados=false,bool incluirInactivos=false); Task<Alojamiento?> ObtenerPorIdAsync(Guid id); Task CrearAsync(Alojamiento a); Task ActualizarAsync(Alojamiento a); Task<bool> DesactivarAsync(Guid id); }
