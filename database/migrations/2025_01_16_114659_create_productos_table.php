<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductosTable extends Migration
{
    /**
     * Ejecuta la migración.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('productos', function (Blueprint $table) {
            $table->id(); // Crea la columna "id" como llave primaria
            $table->timestamps(); // Crea las columnas created_at y updated_at
            $table->string('nombre'); // Nombre del producto
            $table->string('materiales'); // Materiales del producto
            $table->string('cuidados'); // Instrucciones de cuidados
            $table->enum('tamaño', ['xs', 's', 'm', 'l', 'xl', 'xxl', 'xxxl']); // Tamaños posibles
            $table->string('color'); // Color del producto
            $table->string('descripcion'); // Descripción del producto
            $table->string('precio'); // Precio del producto
            $table->string('composicion'); // Composición del producto
            $table->integer('stock'); // Stock del producto
            $table->enum('categoria', ['futbol', 'baloncesto', 'running', 'natacion', 'surf', 'ciclismo', 'skateboarding', 'fitness', 'tenis', 'boxeo']); // Categoría del producto
            $table->enum('genero', ['hombre', 'mujer', 'unisex', 'niño', 'niña']); // Género para el que es el producto
            $table->enum('tipo', ['camiseta', 'pantalon']); // Tipo de prenda

            // Relación con la tabla "users" (suponiendo que ya tienes esta tabla)
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); 
        });
    }

    /**
     * Revierte la migración.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('productos'); // Elimina la tabla "productos"
    }
}
