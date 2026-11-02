async function includeComponent(elementId, file) {
    const element = document.getElementById(elementId);

    try {
        const response = await fetch(file);

        if (!response.ok) {
            throw new Error(`Could not load ${file}`);
        }

        element.innerHTML = await response.text();
    } catch (error) {
        console.error(error);
    }
}

document.addEventListener("DOMContentLoaded", () => {
    includeComponent("header", "components/header.html");
    includeComponent("footer", "components/footer.html");
});
