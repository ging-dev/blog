import * as Stacks from '@stackoverflow/stacks'
import { Controller } from '@hotwired/stimulus'

class ToggleThemeController extends Controller<HTMLInputElement> {
    connect() {
        if (localStorage.getItem("theme") === "dark") {
            this.#enableDarkMode()
        } else {
            this.#disableDarkMode()
        }
    }

    toggle() {
        if (this.element.checked) {
            this.#enableDarkMode()
        } else {
            this.#disableDarkMode()
        }
    }

    #enableDarkMode() {
        document.body.classList.add("theme-dark")
        this.element.checked = true
        localStorage.setItem("theme", "dark")
    }

    #disableDarkMode() {
        document.body.classList.remove("theme-dark")
        this.element.checked = false
        localStorage.setItem("theme", "light")
    }
}

Stacks.application.register("toggle-theme", ToggleThemeController);
