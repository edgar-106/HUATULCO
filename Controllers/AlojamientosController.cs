using HuatulcoTravel.Models;
using HuatulcoTravel.Services;
using Microsoft.AspNetCore.Mvc;

namespace HuatulcoTravel.Controllers;

public class AlojamientosController : Controller
{
    private readonly IAlojamientoService _service;

    public AlojamientosController(IAlojamientoService service)
    {
        _service = service;
    }

    public async Task<IActionResult> Index()
    {
        var alojamientos = await _service.ObtenerTodosAsync(soloPublicados: true);
        return View(alojamientos);
    }

    public async Task<IActionResult> Detalle(Guid id)
    {
        var alojamiento = await _service.ObtenerPorIdAsync(id);
        if (alojamiento == null || !alojamiento.Activo || alojamiento.Estado != EstadoAlojamiento.Publicado)
            return NotFound();

        return View(alojamiento);
    }
}
