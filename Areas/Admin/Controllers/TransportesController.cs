using HuatulcoTravel.Models;
using HuatulcoTravel.Services;
using Microsoft.AspNetCore.Authorization;
using Microsoft.AspNetCore.Mvc;

namespace HuatulcoTravel.Areas.Admin.Controllers;

[Area("Admin")]
[Authorize(Roles = "Administrador")]
public class TransportesController : Controller
{
    private readonly ITransporteService _transportes;
    private readonly IReservacionService _reservaciones;

    public TransportesController(ITransporteService transportes, IReservacionService reservaciones)
    {
        _transportes = transportes;
        _reservaciones = reservaciones;
    }

    public async Task<IActionResult> Index(Guid? reservacionId = null)
    {
        var transportes = await _transportes.ListarAsync(reservacionId);
        var reservaciones = await _reservaciones.ListarAsync();

        ViewData["Reservaciones"] = reservaciones;
        return View(transportes);
    }

    public async Task<IActionResult> Crear()
    {
        var reservaciones = await _reservaciones.ListarAsync();

        var transporte = new Transporte
        {
            FechaRecogida = DateOnly.FromDateTime(DateTime.UtcNow),
            FechaDevolucion = DateOnly.FromDateTime(DateTime.UtcNow.AddDays(1)),
            Estado = EstadoTransporte.Asignado
        };

        ViewData["Reservaciones"] = reservaciones;
        return View(transporte);
    }

    [HttpPost]
    [ValidateAntiForgeryToken]
    public async Task<IActionResult> Crear(Transporte transporte)
    {
        var creado = await _transportes.CrearAsync(transporte);
        return RedirectToAction("Index");
    }

    [HttpPost]
    [ValidateAntiForgeryToken]
    public async Task<IActionResult> Eliminar(Guid id)
    {
        await _transportes.EliminarAsync(id);
        return RedirectToAction("Index");
    }
}
