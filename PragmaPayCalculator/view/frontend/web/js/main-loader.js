define([], function () {
    const mainScriptUrl = 'https://partners-loanfront.box.pragmago.tech/Main.js';

    // Prevent loading it twice
    const alreadyLoaded = !!document.querySelector(`script[src="${mainScriptUrl}"]`);
    if (alreadyLoaded) {
        return;
    }

    const script = document.createElement('script');
    script.type = 'module';
    script.src = mainScriptUrl;

    document.head.appendChild(script);
});
