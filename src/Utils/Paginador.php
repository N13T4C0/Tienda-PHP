<?php
namespace Utils;

/**
 * Paginador generico.
 *
 * Uso:
 *   $paginador = new Paginador($arrayTotal, $porPagina, $paginaActual);
 *   $items     = $paginador->elementosPagina(); // los items de esta pagina
 *
 * En la vista usamos $paginador->totalPaginas(), $paginador->paginaActual(), etc.
 * para renderizar los botones de navegacion.
 */
class Paginador
{
    private array $todos;
    private int   $porPagina;
    private int   $paginaActual;
    private int   $totalPaginas;

    public function __construct(array $todos, int $porPagina = 8, int $paginaActual = 1)
    {
        $this->todos        = $todos;
        $this->porPagina    = max(1, $porPagina);
        $this->totalPaginas = (int) ceil(count($todos) / $this->porPagina);

        // Nos aseguramos de que la pagina pedida este dentro del rango valido
        $this->paginaActual = max(1, min($paginaActual, $this->totalPaginas ?: 1));
    }

    /** Devuelve los elementos que corresponden a la pagina actual */
    public function elementosPagina(): array
    {
        $inicio = ($this->paginaActual - 1) * $this->porPagina;
        return array_slice($this->todos, $inicio, $this->porPagina);
    }

    /** Pagina actual */
    public function paginaActual(): int
    {
        return $this->paginaActual;
    }

    /** Numero total de paginas */
    public function totalPaginas(): int
    {
        return $this->totalPaginas;
    }

    /** Total de elementos (sin paginar) */
    public function totalElementos(): int
    {
        return count($this->todos);
    }

    /** Indica si hay pagina anterior */
    public function hayAnterior(): bool
    {
        return $this->paginaActual > 1;
    }

    /** Indica si hay pagina siguiente */
    public function haySiguiente(): bool
    {
        return $this->paginaActual < $this->totalPaginas;
    }
}
