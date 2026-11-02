async function includeComponent(elementId, file) {
    const element = document.getElementById(elementId);

    if (!element) {
        console.error(`Element #${elementId} was not found.`);
        return;
    }

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

document.addEventListener("DOMContentLoaded", async () => {
    await Promise.all([
        includeComponent("header", "components/header.html"),
        includeComponent("footer", "components/footer.html")
    ]);

    // Run the standards checker after the components load.
    const checker = document.createElement("script");
    checker.src = "https://divonbriesen.github.io/teaching/tools/standards-check.js";
    document.body.appendChild(checker);
});
