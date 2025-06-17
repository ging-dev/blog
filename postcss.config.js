import prefixwrap from "postcss-prefixwrap";
/** @type {import('postcss-load-config').Config} */
const config = {
    plugins: [
        prefixwrap('#editor-container', {
            whitelist: ["editor.css"],
        })
    ]
}

export default config
