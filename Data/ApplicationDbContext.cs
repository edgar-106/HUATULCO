using HuatulcoTravel.Models;
using Microsoft.AspNetCore.Identity;
using Microsoft.AspNetCore.Identity.EntityFrameworkCore;
using Microsoft.EntityFrameworkCore;

namespace HuatulcoTravel.Data;

public class ApplicationDbContext
    : IdentityDbContext<ApplicationUser, IdentityRole<Guid>, Guid>
{
    public ApplicationDbContext(DbContextOptions<ApplicationDbContext> options)
        : base(options) { }

    public DbSet<Alojamiento> Alojamientos => Set<Alojamiento>();
    public DbSet<ImagenAlojamiento> ImagenesAlojamiento => Set<ImagenAlojamiento>();
    public DbSet<Reservacion> Reservaciones => Set<Reservacion>();
    public DbSet<Pago> Pagos => Set<Pago>();
    public DbSet<ComprobantePago> ComprobantesPago => Set<ComprobantePago>();

    protected override void OnModelCreating(ModelBuilder builder)
    {
        base.OnModelCreating(builder);

        builder.Entity<Alojamiento>(entity =>
        {
            entity.HasIndex(x => x.Nombre);
            entity.Property(x => x.PrecioBasePorNoche).HasPrecision(12, 2);
            entity.Property(x => x.Tipo).HasConversion<string>();
            entity.Property(x => x.Estado).HasConversion<string>();
        });

        builder.Entity<ImagenAlojamiento>()
            .HasOne(x => x.Alojamiento)
            .WithMany(x => x.Imagenes)
            .HasForeignKey(x => x.AlojamientoId)
            .OnDelete(DeleteBehavior.Cascade);

        builder.Entity<Reservacion>(entity =>
        {
            entity.HasIndex(x => x.Folio).IsUnique();
            entity.HasIndex(x => new { x.AlojamientoId, x.FechaEntrada, x.FechaSalida });
            entity.Property(x => x.Estado).HasConversion<string>();
            entity.Property(x => x.Subtotal).HasPrecision(12, 2);
            entity.Property(x => x.CargoLimpieza).HasPrecision(12, 2);
            entity.Property(x => x.Descuento).HasPrecision(12, 2);
            entity.Property(x => x.Impuestos).HasPrecision(12, 2);
            entity.Property(x => x.Total).HasPrecision(12, 2);

            entity.ToTable(t => t.HasCheckConstraint(
                "CK_Reservacion_Fechas",
                "\"FechaSalida\" > \"FechaEntrada\""));
        });

        builder.Entity<Pago>(entity =>
        {
            entity.Property(x => x.Importe).HasPrecision(12, 2);
            entity.Property(x => x.Estado).HasConversion<string>();
        });

        builder.Entity<ComprobantePago>()
            .HasOne(x => x.Pago)
            .WithMany(x => x.Comprobantes)
            .HasForeignKey(x => x.PagoId)
            .OnDelete(DeleteBehavior.Cascade);
    }
}
