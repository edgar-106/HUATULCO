using HuatulcoTravel.Models;
using HuatulcoTravel.Services;
using HuatulcoTravel.ViewModels;
using Microsoft.AspNetCore.Authorization;
using Microsoft.AspNetCore.Mvc;

namespace HuatulcoTravel.Areas.Admin.Controllers;

[Area("Admin")]
[Authorize(Roles = "Administrador")]
public class AlojamientosController : Controller
{
    private readonly IAlojamientoService _service;

    public AlojamientosController(IAlojamientoService service)
    {
        _service = service;
    }

    public async Task<IActionResult> Index()
    {
        var datos = await _service.ObtenerTodosAsync();
        return View(datos);
    }

    public IActionResult Crear() => View(new AlojamientoFormViewModel());

    [HttpPost]
    [ValidateAntiForgeryToken]
    public async Task<IActionResult> Crear(AlojamientoFormViewModel model)
    {
        if (!ModelState.IsValid) return View(model);

        var alojamiento = Mapear(model);
        await _service.CrearAsync(alojamiento);

        TempData["Mensaje"] = "El alojamiento se registró correctamente.";
        return RedirectToAction(nameof(Index));
    }

    public async Task<IActionResult> Editar(Guid id)
    {
        var item = await _service.ObtenerPorIdAsync(id);
        if (item is null) return NotFound();

        return View(new AlojamientoFormViewModel
        {
            Id = item.Id,
            Nombre = item.Nombre,
            Descripcion = item.Descripcion,
            Tipo = item.Tipo,
            CapacidadHuespedes = item.CapacidadHuespedes,
            NumeroHabitaciones = item.NumeroHabitaciones,
            NumeroBanos = item.NumeroBanos,
            PrecioBasePorNoche = item.PrecioBasePorNoche,
            Direccion = item.Direccion,
            Latitud = item.Latitud,
            Longitud = item.Longitud,
            Estado = item.Estado
        });
    }

    [HttpPost]
    [ValidateAntiForgeryToken]
    public async Task<IActionResult> Editar(Guid id, AlojamientoFormViewModel model)
    {
        if (id != model.Id) return BadRequest();
        if (!ModelState.IsValid) return View(model);

        var existente = await _service.ObtenerPorIdAsync(id);
        if (existente is null) return NotFound();

        existente.Nombre = model.Nombre;
        existente.Descripcion = model.Descripcion;
        existente.Tipo = model.Tipo;
        existente.CapacidadHuespedes = model.CapacidadHuespedes;
        existente.NumeroHabitaciones = model.NumeroHabitaciones;
        existente.NumeroBanos = model.NumeroBanos;
        existente.PrecioBasePorNoche = model.PrecioBasePorNoche;
        existente.Direccion = model.Direccion;
        existente.Latitud = model.Latitud;
        existente.Longitud = model.Longitud;
        existente.Estado = model.Estado;

        await _service.ActualizarAsync(existente);
        TempData["Mensaje"] = "El alojamiento se actualizó correctamente.";
        return RedirectToAction(nameof(Index));
    }

    [HttpPost]
    [ValidateAntiForgeryToken]
    public async Task<IActionResult> Eliminar(Guid id)
    {
        var eliminado = await _service.EliminarAsync(id);
        if (!eliminado) return NotFound();

        TempData["Mensaje"] = "El alojamiento se eliminó.";
        return RedirectToAction(nameof(Index));
    }

    private static Alojamiento Mapear(AlojamientoFormViewModel model) => new()
    {
        Nombre = model.Nombre,
        Descripcion = model.Descripcion,
        Tipo = model.Tipo,
        CapacidadHuespedes = model.CapacidadHuespedes,
        NumeroHabitaciones = model.NumeroHabitaciones,
        NumeroBanos = model.NumeroBanos,
        PrecioBasePorNoche = model.PrecioBasePorNoche,
        Direccion = model.Direccion,
        Latitud = model.Latitud,
        Longitud = model.Longitud,
        Estado = model.Estado
    };
}
