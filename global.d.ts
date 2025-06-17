declare module '*.css' {
    const classNames: { [className: string]: string };
    export default classNames;
}

declare module '*.module.css' {
    const classNames: { [className: string]: string };
    export default classNames;
}

declare module '*.scss' {
    const content: Record<string, string>;
    export default content;
}
