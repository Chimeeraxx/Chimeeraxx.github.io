type StyleName = "Hello Kitty style" | "Kuromi style" | "Simple background";

type StyleInfo = {
    label: string;
    file: string;
};
//aktualnie wybrany styl
type AppState = {
    currentStyleName: StyleName;
    currentStyleFile: string;
    styles: Record<StyleName, StyleInfo>;
};
//slownik, aktualny styl
const appState: AppState = {
    currentStyleName: "Hello Kitty style",
    currentStyleFile: "style-1.css",

    styles: {
        "Hello Kitty style": {
            label: "Hello Kitty style",
            file: "style-1.css"
        },
        "Kuromi style": {
            label: "Kuromi style",
            file: "style-2.css"
        },
        "Simple background": {
            label: "Simple background",
            file: "style-3.css"
        }
    }
};
//podlaczenie css
function attachStyleLink(styleFile: string): void {
    const oldStyleLink = document.querySelector<HTMLLinkElement>("#active-style");

    if (oldStyleLink !== null) {
        oldStyleLink.remove();
    }

    const newStyleLink = document.createElement("link");

    newStyleLink.id = "active-style";
    newStyleLink.rel = "stylesheet";
    newStyleLink.href = styleFile;

    document.head.appendChild(newStyleLink);
}
//zmienia aktualny styl po kliknieciu w przycisk
function changeStyle(styleName: StyleName): void {
    const selectedStyle = appState.styles[styleName];

    appState.currentStyleName = styleName;
    appState.currentStyleFile = selectedStyle.file;

    attachStyleLink(selectedStyle.file);
    buttonStyles();
}
//panel z przyciskami do zmiany stylow
function buttonStyles(): void {
    let switcher = document.querySelector<HTMLDivElement>("#style-switcher");

    if (switcher === null) {
        switcher = document.createElement("div");
        switcher.id = "style-switcher";

        document.body.prepend(switcher);
    }

    switcher.innerHTML = "";

    const title = document.createElement("p");
    title.textContent = "Choose your character:";
    switcher.appendChild(title);

    Object.keys(appState.styles).forEach((styleName) => {
        const typedStyleName = styleName as StyleName;
        const styleData = appState.styles[typedStyleName];

        const button = document.createElement("button");

        button.type = "button";
        button.textContent = styleData.label;

        if (typedStyleName === appState.currentStyleName) {
            button.classList.add("active-style-button");
        }

        button.addEventListener("click", () => changeStyle(typedStyleName));

        switcher.appendChild(button);
    });
}
//domyslny styl
attachStyleLink(appState.currentStyleFile);
buttonStyles();