using HuatulcoTravel.Data;using HuatulcoTravel.Models;using Microsoft.EntityFrameworkCore;namespace HuatulcoTravel.Services;
public class AlojamientoService:IAlojamientoService{private readonly ApplicationDbContext _db;public AlojamientoService(ApplicationDbContext db)=>_db=db;
 public Task<List<Alojamiento>> ObtenerTodosAsync(bool soloPublicados=false,bool incluirInactivos=false){var q=_db.Alojamientos.AsNoTracking().Include(x=>x.Imagenes).AsQueryable();if(!incluirInactivos)q=q.Where(x=>x.Activo);if(soloPublicados)q=q.Where(x=>x.Estado==EstadoAlojamiento.Publicado);return q.OrderBy(x=>x.Nombre).ToListAsync();}
 public Task<Alojamiento?> ObtenerPorIdAsync(Guid id)=>_db.Alojamientos.Include(x=>x.Imagenes).FirstOrDefaultAsync(x=>x.Id==id&&x.Activo);
 public async Task CrearAsync(Alojamiento a){a.Id=Guid.NewGuid();a.FechaCreacionUtc=DateTime.UtcNow;a.Activo=true;_db.Alojamientos.Add(a);await _db.SaveChangesAsync();}
 public async Task ActualizarAsync(Alojamiento a){a.FechaActualizacionUtc=DateTime.UtcNow;await _db.SaveChangesAsync();}
 public async Task<bool> DesactivarAsync(Guid id){var a=await _db.Alojamientos.FirstOrDefaultAsync(x=>x.Id==id&&x.Activo);if(a==null)return false;a.Activo=false;a.Estado=EstadoAlojamiento.NoPublicado;a.FechaActualizacionUtc=DateTime.UtcNow;await _db.SaveChangesAsync();return true;}
}
