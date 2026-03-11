import "./bootstrap";
import Alpine from "alpinejs";

if (!window.Alpine) {
    window.Alpine = Alpine;

    document.addEventListener("alpine:init", () => {
        window.chartInstances = window.chartInstances || [];
    });

    Alpine.start();
}
