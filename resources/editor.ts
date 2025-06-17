import { EditorType, StacksEditor, Utils } from "@stackoverflow/stacks-editor";
import { Plugin } from "prosemirror-state"
import "./editor.css";
import "./hide.css";

(function ($) {
    const editorContainer = $("#editor-container");
    const textArea = $("#content") as JQuery<HTMLTextAreaElement>;
    const plugin = new Plugin({
        view() {
            return {
                update(view, prevState) {
                    if (Utils.docChanged(view.state, prevState)) {
                        Utils.dispatchEditorEvent(view.dom, "change");
                    }
                }
            }
        }
    });
    const editor = new StacksEditor(editorContainer[0], textArea.val() as string, {
        imageUpload: {
            async handler(file) {
                const formData = new FormData();
                formData.append("key", "e53ebd96311a7517c4cd255a27ddc79c");
                formData.append("image", file);
                const json = await $.post({
                    url: "https://api.imgbb.com/1/upload",
                    data: formData,
                    dataType: "json",
                    processData: false,
                    contentType: false
                }) as { data: { url: string } };

                return json.data.url;
            },
            brandingHtml: "",
            contentPolicyHtml: ""
        },
        editorPlugins: [
            () => ({
                commonmark: {
                    plugins: [plugin]
                },
                richText: {
                    plugins: [plugin]
                }
            })
        ],
        defaultView: EditorType.Commonmark
    });
    editorContainer.on("StacksEditor:change", () => {
        textArea.val(editor.content);
        textArea.trigger("input");
    });
})(jQuery);
