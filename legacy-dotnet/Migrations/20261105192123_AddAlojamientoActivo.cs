using Microsoft.EntityFrameworkCore.Migrations;

#nullable disable

namespace HuatulcoTravel.Migrations
{
    /// <inheritdoc />
    public partial class AddAlojamientoActivo : Migration
    {
        /// <inheritdoc />
        protected override void Up(MigrationBuilder migrationBuilder)
        {
            migrationBuilder.AddColumn<bool>(
                name: "Activo",
                table: "Alojamientos",
                type: "boolean",
                nullable: false,
                defaultValue: false);
        }

        /// <inheritdoc />
        protected override void Down(MigrationBuilder migrationBuilder)
        {
            migrationBuilder.DropColumn(
                name: "Activo",
                table: "Alojamientos");
        }
    }
}
