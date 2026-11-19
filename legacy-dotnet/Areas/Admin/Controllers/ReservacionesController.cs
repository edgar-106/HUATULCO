using HuatulcoTravel.Models;
using HuatulcoTravel.Services;
using Microsoft.AspNetCore.Authorization;
using Microsoft.AspNetCore.Mvc;

namespace HuatulcoTravel.Areas.Admin.Controllers;

[Area("Admin")]
[Authorize(Roles = "Administrador")]
public class ReservacionesController : Controller
{
    private readonly IReservacionService _reservaciones;
    private readonly IAlojamientoService _alojamientos;

    public ReservacionesController(IReservacionService reservaciones, IAlojamientoService alojamientos)
    {
        _reservaciones = reservaciones;
        _alojamientos = alojamientos;
    }

    public async Task<IActionResult> Index(Guid? alojamientoId = null, EstadoReservacion? estado = null)
    {
        var reservaciones = await _reservaciones.ListarAsync(alojamientoId, estado);
        var alojamientos = await _alojamientos.ObtenerTodosAsync(incluirInactivos: true);

        ViewData["Alojamientos"] = alojamientos;
        ViewData["AlojamientoId"] = alojamientoId;
        ViewData["Estado"] = estado;
        return View(reservaciones);
    }

    public async Task<IActionResult> Detalle(Guid id)
    {
        var reservacion = await _reservaciones.ObtenerPorIdAsync(id);

        if (reservacion == null)
        {
            return NotFound();
        }

        return View(reservacion);
    }

    [HttpPost]
    [ValidateAntiForgeryToken]
    public async Task<IActionResult> Cancelar(Guid id)
    {
        await _reservaciones.CancelarAsync(id);
        return RedirectToAction("Detalle", new { id });
    }

    [HttpPost]
    [ValidateAntiForgeryToken]
    public async Task<IActionResult> Confirmar(Guid id)
    {
        await _reservaciones.ConfirmarAsync(id);
        return RedirectToAction("Detalle", new { id });
    }
}
