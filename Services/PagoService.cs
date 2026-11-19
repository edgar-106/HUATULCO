using HuatulcoTravel.Data;
using HuatulcoTravel.Models;
using Microsoft.EntityFrameworkCore;

namespace HuatulcoTravel.Services;

public class PagoService : IPagoService
{
    private readonly ApplicationDbContext _db;

    public PagoService(ApplicationDbContext db)
    {
        _db = db;
    }

    public async Task<List<Pago>> ListarPendientesRevisionAsync()
    {
        return await _db.Pagos
            .Include(x => x.Reservacion)!.ThenInclude(x => x.Alojamiento)
            .Include(x => x.Reservacion)!.ThenInclude(x => x.Usuario)
            .Include(x => x.Comprobantes)
            .Where(x => x.Estado == EstadoPago.EnRevision)
            .OrderByDescending(x => x.FechaRegistroUtc)
            .ToListAsync();
    }

    public async Task<Pago?> ObtenerPorIdAsync(Guid id)
    {
        return await _db.Pagos
            .Include(x => x.Reservacion)!.ThenInclude(x => x.Alojamiento)
            .Include(x => x.Reservacion)!.ThenInclude(x => x.Usuario)
            .Include(x => x.Comprobantes)
            .FirstOrDefaultAsync(x => x.Id == id);
    }

    public async Task<bool> AprobarAsync(Guid id, Guid adminId)
    {
        var pago = await _db.Pagos
            .Include(x => x.Reservacion)
            .FirstOrDefaultAsync(x => x.Id == id);

        if (pago == null || pago.Estado != EstadoPago.EnRevision)
        {
            return false;
        }

        pago.Estado = EstadoPago.Aprobado;
        pago.FechaRevisionUtc = DateTime.UtcNow;
        pago.RevisadoPorId = adminId;

        if (pago.Reservacion != null)
        {
            pago.Reservacion.Estado = EstadoReservacion.Confirmada;
        }

        await _db.SaveChangesAsync();
        return true;
    }

    public async Task<bool> RechazarAsync(Guid id, Guid adminId)
    {
        var pago = await _db.Pagos
            .Include(x => x.Reservacion)
            .FirstOrDefaultAsync(x => x.Id == id);

        if (pago == null || pago.Estado != EstadoPago.EnRevision)
        {
            return false;
        }

        pago.Estado = EstadoPago.Rechazado;
        pago.FechaRevisionUtc = DateTime.UtcNow;
        pago.RevisadoPorId = adminId;

        if (pago.Reservacion != null)
        {
            pago.Reservacion.Estado = EstadoReservacion.Rechazada;
        }

        await _db.SaveChangesAsync();
        return true;
    }
}
