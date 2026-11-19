using HuatulcoTravel.Data;
using HuatulcoTravel.Models;
using Microsoft.EntityFrameworkCore;

namespace HuatulcoTravel.Services;

public class EventoService : IEventoService
{
    private readonly ApplicationDbContext _db;

    public EventoService(ApplicationDbContext db)
    {
        _db = db;
    }

    public async Task<List<Evento>> ListarAsync(Guid? alojamientoId = null, TipoEvento? tipo = null, bool? esSporadico = null, EstadoEvento? estado = null)
    {
        var query = _db.Evento
            .Include(x => x.Alojamiento)
            .Include(x => x.Usuario)
            .AsQueryable();

        if (alojamientoId.HasValue)
        {
            query = query.Where(x => x.AlojamientoId == alojamientoId.Value);
        }

        if (tipo.HasValue)
        {
            query = query.Where(x => x.Tipo == tipo.Value);
        }

        if (esSporadico.HasValue)
        {
            query = query.Where(x => x.EsSporadico == esSporadico.Value);
        }

        if (estado.HasValue)
        {
            query = query.Where(x => x.Estado == estado.Value);
        }

        return await query
            .OrderBy(x => x.Fecha)
            .ThenBy(x => x.Nombre)
            .ToListAsync();
    }

    public async Task<List<Evento>> ListarPorAlojamientoAsync(Guid alojamientoId)
    {
        return await _db.Evento
            .Where(x => x.AlojamientoId == alojamientoId)
            .OrderBy(x => x.Fecha)
            .ThenBy(x => x.Nombre)
            .ToListAsync();
    }

    public async Task<Evento?> ObtenerPorIdAsync(Guid id)
    {
        return await _db.Evento
            .Include(x => x.Alojamiento)
            .Include(x => x.Usuario)
            .FirstOrDefaultAsync(x => x.Id == id);
    }

    public async Task<bool> CrearAsync(Evento evento)
    {
        evento.Id = Guid.NewGuid();
        evento.FechaCreacionUtc = DateTime.UtcNow;
        evento.Estado = EstadoEvento.Programada;

        _db.Evento.Add(evento);
        await _db.SaveChangesAsync();
        return true;
    }

    public async Task<bool> ActualizarAsync(Evento evento)
    {
        evento.FechaActualizacionUtc = DateTime.UtcNow;

        _db.Evento.Update(evento);
        await _db.SaveChangesAsync();
        return true;
    }

    public async Task<bool> EliminarAsync(Guid id)
    {
        var evento = await _db.Evento.FirstOrDefaultAsync(x => x.Id == id);

        if (evento == null)
        {
            return false;
        }

        _db.Evento.Remove(evento);
        await _db.SaveChangesAsync();
        return true;
    }
}
