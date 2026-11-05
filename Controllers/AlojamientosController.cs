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
}
