using System.ComponentModel.DataAnnotations;
namespace HuatulcoTravel.ViewModels;
public class RegistroViewModel {
 [Required, StringLength(80)] [Display(Name="Nombre")] public string Nombre {get;set;}=string.Empty;
 [StringLength(100)] [Display(Name="Apellidos")] public string? Apellidos {get;set;}
 [Required,EmailAddress,StringLength(180)] [Display(Name="Correo electrónico")] public string Email {get;set;}=string.Empty;
 [Required,Phone,StringLength(30)] [Display(Name="Teléfono")] public string Telefono {get;set;}=string.Empty;
 [Required,DataType(DataType.Password),MinLength(10)] [Display(Name="Contraseña")] public string Password {get;set;}=string.Empty;
 [Required,DataType(DataType.Password),Compare(nameof(Password),ErrorMessage="Las contraseñas no coinciden.")] [Display(Name="Confirmar contraseña")] public string ConfirmarPassword {get;set;}=string.Empty;
}
