<?php

namespace Utils;

use FPDF;

/** Genera la factura PDF de un pedido usando FPDF */
class GeneradorFactura extends FPDF
{
    /** Cabecera del PDF */
    public function Header(): void
    {
        $this->SetFont('Arial', 'B', 16);
        $this->SetTextColor(30, 58, 95);
        $this->Cell(0, 10, 'netStore', 0, 1, 'L');
        $this->SetFont('Arial', '', 9);
        $this->SetTextColor(120, 120, 120);
        $this->Cell(0, 5, 'Factura de compra', 0, 1, 'L');
        $this->Ln(4);
        $this->SetDrawColor(200, 200, 200);
        $this->Line(10, $this->GetY(), 200, $this->GetY());
        $this->Ln(4);
    }

    /** Pie de pagina */
    public function Footer(): void
    {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->SetTextColor(150, 150, 150);
        $this->Cell(0, 10, 'Pagina ' . $this->PageNo(), 0, 0, 'C');
    }

    /**
     * Genera el PDF de la factura y lo envia al navegador para descargar.
     * Se llama desde PagoControlador::factura()
     */
    public static function descargar(array $pedido, array $lineas): void
    {
        $pdf = new self('P', 'mm', 'A4');
        $pdf->AddPage();
        $pdf->SetAutoPageBreak(true, 20);

        // Datos del pedido
        $pdf->SetFont('Arial', 'B', 11);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->Cell(0, 7, 'Factura #' . $pedido['id'], 0, 1);
        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell(0, 6, 'Fecha: ' . date('d/m/Y H:i', strtotime($pedido['fecha_pedido'])), 0, 1);
        $pdf->Cell(0, 6, 'Cliente: ' . $pedido['usuario_nombre'] . ' (' . $pedido['usuario_email'] . ')', 0, 1);
        $pdf->Cell(0, 6, 'Direccion: ' . $pedido['direccion'] . ', ' . $pedido['localidad'] . ' (' . $pedido['provincia'] . ')', 0, 1);
        $pdf->Ln(6);

        // Cabecera de la tabla
        $pdf->SetFillColor(240, 244, 248);
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(80, 8, 'Producto',    1, 0, 'L', true);
        $pdf->Cell(25, 8, 'Unidades',    1, 0, 'C', true);
        $pdf->Cell(40, 8, 'Precio/ud',   1, 0, 'R', true);
        $pdf->Cell(40, 8, 'Subtotal',    1, 1, 'R', true);

        // Filas de productos
        $pdf->SetFont('Arial', '', 10);
        $pdf->SetFillColor(255, 255, 255);

        foreach ($lineas as $linea) {
            $pdf->Cell(80, 7, $linea['nombre_producto'], 1, 0, 'L');
            $pdf->Cell(25, 7, $linea['unidades'],        1, 0, 'C');
            $pdf->Cell(40, 7, number_format($linea['precio_unidad'], 2, ',', '.') . ' EUR', 1, 0, 'R');
            $pdf->Cell(40, 7, number_format($linea['subtotal'],      2, ',', '.') . ' EUR', 1, 1, 'R');
        }

        // Total
        $pdf->SetFont('Arial', 'B', 11);
        $pdf->Cell(145, 8, 'TOTAL', 1, 0, 'R');
        $pdf->Cell(40, 8, number_format($pedido['importe_total'], 2, ',', '.') . ' EUR', 1, 1, 'R');

        // Envia el PDF al navegador como descarga
        $pdf->Output('D', 'factura_pedido_' . $pedido['id'] . '.pdf');
    }
}
