using HuatulcoTravel.Data;
using HuatulcoTravel.Models;
using Microsoft.EntityFrameworkCore;

namespace HuatulcoTravel.Services;

public class AlojamientoService : IAlojamientoService
{
    private readonly ApplicationDbContext _context;

    public AlojamientoService(ApplicationDbContext context)
    {
        _context = context;
    }

    public Task<List<Alojamiento>> ObtenerTodosAsync(bool soloPublicados = false)
    {
        var query = _context.Alojamientos.AsNoTracking()
            .Include(a => a.Imagenes)
            .AsQueryable();

        if (soloPublicados)
            query = query.Where(a => a.Estado == EstadoAlojamiento.Publicado);

        return query.OrderBy(a => a.Nombre).ToListAsync();
    }

    public Task<Alojamiento?> ObtenerPorIdAsync(Guid id) =>
        _context.Alojamientos
            .Include(a => a.Imagenes)
            .FirstOrDefaultAsync(a => a.Id == id);

    public async Task CrearAsync(Alojamiento alojamiento)
    {
        alojamiento.Id = Guid.NewGuid();
        alojamiento.FechaCreacionUtc = DateTime.UtcNow;
        _context.Alojamientos.Add(alojamiento);
        await _context.SaveChangesAsync();
    }

    public async Task ActualizarAsync(Alojamiento alojamiento)
    {
        alojamiento.FechaActualizacionUtc = DateTime.UtcNow;
        _context.Alojamientos.Update(alojamiento);
        await _context.SaveChangesAsync();
    }

    public async Task<bool> EliminarAsync(Guid id)
    {
        var alojamiento = await _context.Alojamientos.FindAsync(id);
        if (alojamiento is null) return false;

        // En producción: preferir baja lógica cuando existan reservaciones históricas.
        _context.Alojamientos.Remove(alojamiento);
        await _context.SaveChangesAsync();
        return true;
    }
}
