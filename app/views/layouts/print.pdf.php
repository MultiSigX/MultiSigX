<?php
header("Content-type: application/pdf");
echo $this->Pdf->Output(QR_OUTPUT_DIR.'x-'.$printdata['username']."-MSX-Print".".pdf","F");
?>