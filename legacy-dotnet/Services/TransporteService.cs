using HuatulcoTravel.Data;
using HuatulcoTravel.Models;
using Microsoft.EntityFrameworkCore;

namespace HuatulcoTravel.Services;

public class TransporteService : ITransporteService
{
    private readonly ApplicationDbContext _db;

    public TransporteService(ApplicationDbContext db)
    {
        _db = db;
    }

    public async Task<List<Transporte>> ListarAsync(Guid? reservacionId = null)
    {
        var query = _db.Transporte
            .Include(x => x.Reservacion).ThenInclude(x => x.Alojamiento)
            .Include(x => x.Reservacion).ThenInclude(x => x.Usuario)
            .AsQueryable();

        if (reservacionId.HasValue)
        {
            query = query.Where(x => x.ReservacionId == reservacionId.Value);
        }

        return await query
            .OrderByDescending(x => x.FechaCreacionUtc)
            .ToListAsync();
    }

    public async Task<Transporte?> ObtenerPorIdAsync(Guid id)
    {
        return await _db.Transporte
            .Include(x => x.Reservacion).ThenInclude(x => x.Alojamiento)
            .Include(x => x.Reservacion).ThenInclude(x => x.Usuario)
            .FirstOrDefaultAsync(x => x.Id == id);
    }

    public async Task<bool> CrearAsync(Transporte transporte)
    {
        transporte.Id = Guid.NewGuid();
        transporte.FechaCreacionUtc = DateTime.UtcNow;

        _db.Transporte.Add(transporte);
        await _db.SaveChangesAsync();
        return true;
    }

    public async Task<bool> ActualizarAsync(Transporte transporte)
    {
        _db.Transporte.Update(transporte);
        await _db.SaveChangesAsync();
        return true;
    }

    public async Task<bool> EliminarAsync(Guid id)
    {
        var transporte = await _db.Transporte.FirstOrDefaultAsync(x => x.Id == id);

        if (transporte == null)
        {
            return false;
        }

        _db.Transporte.Remove(transporte);
        await _db.SaveChangesAsync();
        return true;
    }
}
