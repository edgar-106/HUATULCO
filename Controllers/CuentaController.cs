using HuatulcoTravel.Models; using HuatulcoTravel.ViewModels; using Microsoft.AspNetCore.Identity; using Microsoft.AspNetCore.Mvc;
namespace HuatulcoTravel.Controllers;
public class CuentaController : Controller {
 private readonly SignInManager<ApplicationUser> _signIn; private readonly UserManager<ApplicationUser> _users;
 public CuentaController(SignInManager<ApplicationUser> signIn,UserManager<ApplicationUser> users){_signIn=signIn;_users=users;}
 [HttpGet] public IActionResult Ingresar(string? returnUrl=null)=>View(new LoginViewModel{ReturnUrl=returnUrl});
 [HttpPost,ValidateAntiForgeryToken] public async Task<IActionResult> Ingresar(LoginViewModel model){
  if(!ModelState.IsValid)return View(model); var user=await _users.FindByEmailAsync(model.Email.Trim());
  if(user is null||!user.Activo){ModelState.AddModelError(string.Empty,"Correo o contraseña incorrectos.");return View(model);}
  var result=await _signIn.PasswordSignInAsync(user.UserName!,model.Password,model.RememberMe,true);
  if(result.Succeeded){if(!string.IsNullOrWhiteSpace(model.ReturnUrl)&&Url.IsLocalUrl(model.ReturnUrl))return LocalRedirect(model.ReturnUrl); if(await _users.IsInRoleAsync(user,"Administrador"))return RedirectToAction("Index","Alojamientos",new{area="Admin"}); return RedirectToAction("Index","Home");}
  ModelState.AddModelError(string.Empty,result.IsLockedOut?"Cuenta bloqueada temporalmente.":"Correo o contraseña incorrectos."); return View(model);
 }
 [HttpGet] public IActionResult Registro(){if(User.Identity?.IsAuthenticated==true)return RedirectToAction("Index","Home");return View(new RegistroViewModel());}
 [HttpPost,ValidateAntiForgeryToken] public async Task<IActionResult> Registro(RegistroViewModel model){
  if(!ModelState.IsValid)return View(model); var user=new ApplicationUser{Id=Guid.NewGuid(),UserName=model.Email.Trim(),Email=model.Email.Trim(),PhoneNumber=model.Telefono.Trim(),Nombre=model.Nombre.Trim(),Apellidos=model.Apellidos?.Trim(),FechaRegistroUtc=DateTime.UtcNow,Activo=true};
  var result=await _users.CreateAsync(user,model.Password); if(!result.Succeeded){foreach(var e in result.Errors)ModelState.AddModelError(string.Empty,e.Description);return View(model);}
  await _users.AddToRoleAsync(user,"Cliente"); await _signIn.SignInAsync(user,false); return RedirectToAction("Index","Home");
 }
 [HttpPost,ValidateAntiForgeryToken] public async Task<IActionResult> Salir(){await _signIn.SignOutAsync();return RedirectToAction("Index","Home");}
 [HttpGet] public IActionResult AccesoDenegado()=>View();
}
