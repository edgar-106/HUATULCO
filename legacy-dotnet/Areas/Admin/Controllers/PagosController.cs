using System.Security.Claims;
using HuatulcoTravel.Models;
using HuatulcoTravel.Services;
using Microsoft.AspNetCore.Authorization;
using Microsoft.AspNetCore.Mvc;

namespace HuatulcoTravel.Areas.Admin.Controllers;

[Area("Admin")]
[Authorize(Roles = "Administrador")]
public class PagosController : Controller
{
    private readonly IPagoService _pagos;

    public PagosController(IPagoService pagos)
    {
        _pagos = pagos;
    }

    public async Task<IActionResult> Index()
    {
        var pendientes = await _pagos.ListarPendientesRevisionAsync();
        return View(pendientes);
    }

    [HttpPost]
    [ValidateAntiForgeryToken]
    public async Task<IActionResult> Aprobar(Guid id)
    {
        var adminId = Guid.Empty;
        var nameIdentifier = User.FindFirstValue(ClaimTypes.NameIdentifier);
        if (Guid.TryParse(nameIdentifier, out var parsed))
        {
            adminId = parsed;
        }

        await _pagos.AprobarAsync(id, adminId);
        return RedirectToAction("Index");
    }

    [HttpPost]
    [ValidateAntiForgeryToken]
    public async Task<IActionResult> Rechazar(Guid id)
    {
        var adminId = Guid.Empty;
        var nameIdentifier = User.FindFirstValue(ClaimTypes.NameIdentifier);
        if (Guid.TryParse(nameIdentifier, out var parsed))
        {
            adminId = parsed;
        }

        await _pagos.RechazarAsync(id, adminId);
        return RedirectToAction("Index");
    }
}
