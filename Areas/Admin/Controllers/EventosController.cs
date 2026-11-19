using HuatulcoTravel.Models;
using HuatulcoTravel.Services;
using Microsoft.AspNetCore.Authorization;
using Microsoft.AspNetCore.Mvc;
using Microsoft.AspNetCore.Identity;

namespace HuatulcoTravel.Areas.Admin.Controllers;

[Area("Admin")]
[Authorize(Roles = "Administrador")]
public class EventosController : Controller
{
    private readonly IEventoService _eventos;
    private readonly IAlojamientoService _alojamientos;
    private readonly UserManager<ApplicationUser> _users;

    public EventosController(IEventoService eventos, IAlojamientoService alojamientos, UserManager<ApplicationUser> users)
    {
        _eventos = eventos;
        _alojamientos = alojamientos;
        _users = users;
    }

    public async Task<IActionResult> Index(Guid? alojamientoId = null, TipoEvento? tipo = null, bool? esSporadico = null, EstadoEvento? estado = null)
    {
        var eventos = await _eventos.ListarAsync(alojamientoId, tipo, esSporadico, estado);
        var alojamientos = await _alojamientos.ObtenerTodosAsync(incluirInactivos: true);

        ViewData["Alojamientos"] = alojamientos;
        ViewData["AlojamientoId"] = alojamientoId;
        ViewData["Tipo"] = tipo;
        ViewData["EsSporadico"] = esSporadico;
        return View(eventos);
    }

    public async Task<IActionResult> Crear()
    {
        var alojamientos = await _alojamientos.ObtenerTodosAsync(incluirInactivos: true);
        var usuarios = _users.Users.ToList();

        var evento = new Evento
        {
            AlojamientoId = alojamientos.FirstOrDefault()?.Id ?? Guid.Empty,
            Fecha = DateOnly.FromDateTime(DateTime.UtcNow.AddDays(30)),
            Estado = EstadoEvento.Programada
        };

        ViewData["Alojamientos"] = alojamientos;
        ViewData["Usuarios"] = usuarios;
        return View(evento);
    }

    [HttpPost]
    [ValidateAntiForgeryToken]
    public async Task<IActionResult> Crear(Evento evento, Guid? usuarioId)
    {
        if (!evento.AlojamientoId.Equals(Guid.Empty))
        {
            evento.UsuarioId = usuarioId;
            var creado = await _eventos.CrearAsync(evento);
            return RedirectToAction("Index");
        }

        var alojamientos = await _alojamientos.ObtenerTodosAsync(incluirInactivos: true);
        ViewData["Alojamientos"] = alojamientos;
        ViewData["Usuarios"] = _users.Users.ToList();
        return View(evento);
    }

    [HttpPost]
    [ValidateAntiForgeryToken]
    public async Task<IActionResult> Eliminar(Guid id)
    {
        await _eventos.EliminarAsync(id);
        return RedirectToAction("Index");
    }
}
