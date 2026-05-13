<?php

namespace Lib;

use Fpdf\Fpdf;

class GeneradorRecibo
{
    public static function generar(
        int    $idPedido,
        string $nombre,
        array  $items,
        float  $total,
        array  $envio
    ): string {
        $pdf = new Fpdf();
        $pdf->AddPage();
        $pdf->SetFont('Arial', 'B', 16);

        // Cabecera
        $pdf->SetFillColor(44, 62, 80);
        $pdf->SetTextColor(255, 255, 255);
        $pdf->Cell(0, 12, 'netStore - Recibo de pedido', 0, 1, 'C', true);
        $pdf->Ln(4);

        // Datos del pedido
        $pdf->SetFont('Arial', '', 11);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->Cell(0, 7, 'Pedido #' . $idPedido, 0, 1);
        $pdf->Cell(0, 7, 'Cliente: ' . $nombre, 0, 1);
        $pdf->Cell(0, 7, 'Fecha: ' . date('d/m/Y H:i'), 0, 1);
        $pdf->Cell(0, 7, 'Direccion: ' . $envio['direccion'] . ', ' . $envio['localidad'] . ' (' . ($envio['provincia'] ?? '') . ')', 0, 1);
        $pdf->Ln(4);

        // Cabecera tabla
        $pdf->SetFont('Arial', 'B', 11);
        $pdf->SetFillColor(236, 240, 241);
        $pdf->Cell(80, 8, 'Producto', 1, 0, 'L', true);
        $pdf->Cell(30, 8, 'Uds.', 1, 0, 'C', true);
        $pdf->Cell(40, 8, 'Precio', 1, 0, 'R', true);
        $pdf->Cell(40, 8, 'Subtotal', 1, 1, 'R', true);

        // Filas de productos
        $pdf->SetFont('Arial', '', 10);
        foreach ($items as $item) {
            $p        = $item['producto'];
            $cantidad = (int) $item['cantidad'];
            $subtotal = $p->precio * $cantidad;

            $pdf->Cell(80, 7, $p->nombre, 1, 0, 'L');
            $pdf->Cell(30, 7, $cantidad, 1, 0, 'C');
            $pdf->Cell(40, 7, number_format($p->precio, 2) . ' EUR', 1, 0, 'R');
            $pdf->Cell(40, 7, number_format($subtotal, 2) . ' EUR', 1, 1, 'R');
        }

        // Total
        $pdf->SetFont('Arial', 'B', 11);
        $pdf->Cell(110, 8, '', 0);
        $pdf->Cell(40, 8, 'TOTAL', 1, 0, 'R');
        $pdf->Cell(40, 8, number_format($total, 2) . ' EUR', 1, 1, 'R');

        // Pie
        $pdf->Ln(8);
        $pdf->SetFont('Arial', 'I', 10);
        $pdf->SetTextColor(120, 120, 120);
        $pdf->Cell(0, 7, 'Gracias por tu compra en netStore.', 0, 1, 'C');

        return $pdf->Output('S');
    }
}
