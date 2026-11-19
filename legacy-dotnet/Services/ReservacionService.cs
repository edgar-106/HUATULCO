using HuatulcoTravel.Data;
using HuatulcoTravel.Models;
using Microsoft.EntityFrameworkCore;

namespace HuatulcoTravel.Services;

public class ReservacionService : IReservacionService
{
    private readonly ApplicationDbContext _db;

    public ReservacionService(ApplicationDbContext db)
    {
        _db = db;
    }

    public async Task<List<Reservacion>> ListarAsync(Guid? alojamientoId = null, EstadoReservacion? estado = null)
    {
        var query = _db.Reservaciones
            .Include(x => x.Alojamiento)
            .Include(x => x.Usuario)
            .Include(x => x.Pagos)
            .Include(x => x.Eventos)
            .Include(x => x.Transportes)
            .AsQueryable();

        if (alojamientoId.HasValue)
        {
            query = query.Where(x => x.AlojamientoId == alojamientoId.Value);
        }

        if (estado.HasValue)
        {
            query = query.Where(x => x.Estado == estado.Value);
        }

        return await query
            .OrderByDescending(x => x.FechaCreacionUtc)
            .ToListAsync();
    }

    public async Task<Reservacion?> ObtenerPorIdAsync(Guid id)
    {
        return await _db.Reservaciones
            .Include(x => x.Alojamiento)
            .Include(x => x.Usuario)
            .Include(x => x.Pagos)
            .Include(x => x.Eventos)
            .Include(x => x.Transportes)
            .FirstOrDefaultAsync(x => x.Id == id);
    }

    public async Task<bool> CancelarAsync(Guid id)
    {
        var reservacion = await _db.Reservaciones.FirstOrDefaultAsync(x => x.Id == id);

        if (reservacion == null)
        {
            return false;
        }

        if (reservacion.Estado is EstadoReservacion.Cancelada
            or EstadoReservacion.Finalizada
            or EstadoReservacion.Expirada)
        {
            return false;
        }

        reservacion.Estado = EstadoReservacion.Cancelada;
        await _db.SaveChangesAsync();
        return true;
    }

    public async Task<bool> ConfirmarAsync(Guid id)
    {
        var reservacion = await _db.Reservaciones.FirstOrDefaultAsync(x => x.Id == id);

        if (reservacion == null)
        {
            return false;
        }

        var estadosConfirmables = new[]
        {
            EstadoReservacion.PendienteDePago,
            EstadoReservacion.ComprobanteEnviado,
            EstadoReservacion.EnRevision
        };

        if (!estadosConfirmables.Contains(reservacion.Estado))
        {
            return false;
        }

        reservacion.Estado = EstadoReservacion.Confirmada;
        await _db.SaveChangesAsync();
        return true;
    }
}
