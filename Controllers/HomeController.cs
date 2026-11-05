using HuatulcoTravel.Services;
using Microsoft.AspNetCore.Mvc;

namespace HuatulcoTravel.Controllers;

public class HomeController : Controller
{
    private readonly IAlojamientoService _alojamientos;

    public HomeController(IAlojamientoService alojamientos)
    {
        _alojamientos = alojamientos;
    }

    public async Task<IActionResult> Index()
    {
        var propiedades = await _alojamientos.ObtenerTodosAsync(soloPublicados: true);
        return View(propiedades.Take(6));
    }

    public IActionResult Privacy() => View();

    public IActionResult Error() => View();
}
