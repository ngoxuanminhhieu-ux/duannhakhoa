try {
    $ppt = New-Object -ComObject PowerPoint.Application
    $pres = $ppt.Presentations.Open('C:\Duannhakhoa\BAO_CAO_DU_AN_NHA_KHOA_ICTU.pptx', -1, 0, 0)
    $outDir = 'C:\Duannhakhoa\presentation_assets\preview_slides'
    $pres.Slides.Item(1).Export("$outDir\slide_1.png", "PNG", 1920, 1080)
    $pres.Slides.Item(21).Export("$outDir\slide_21.png", "PNG", 1920, 1080)
    $pres.Close()
    $ppt.Quit()
    [System.Runtime.InteropServices.Marshal]::ReleaseComObject($ppt) | Out-Null
    Write-Host "Exported 1 and 21 successfully!"
} catch {
    Write-Host "Error: $($_.Exception.Message)"
}
