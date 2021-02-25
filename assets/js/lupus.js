const body = document.querySelector('body')

function updateImage(new_image) {
    if(parseInt(new_image) != -1) {
        // cambiando valore input
        document.querySelector('#theme_image').value = new_image
        
        document.querySelector('#bg').setAttribute("style", "background-image: url('assets/img/bg/"+new_image+".jpg')")
        // selezione
        document.querySelectorAll('#bgs_list>.selector_entry').forEach(element => {element.classList.remove('selected')})
        document.querySelector('#bgs_list>.selector_entry.se-'+new_image).classList.add('selected')
    } else {
        // cambiando valore input
        document.querySelector('#theme_image').value = -1

        document.querySelectorAll('#bgs_list>.selector_entry').forEach(element => {element.classList.remove('selected')})
        document.querySelector('#bgs_list>.selector_entry.se-random').classList.add('selected')
    }
}
function updateColor(new_color) {
    if(parseInt(new_color) != -1) {
        if(body.classList.contains('clr-0') | body.classList.contains('bs-clr-0')) {
            clr = 0
        }
        else if(body.classList.contains('clr-1') | body.classList.contains('bs-clr-1')) {
            clr = 1        
        }
        else if(body.classList.contains('clr-2') | body.classList.contains('bs-clr-2')) {
            clr = 2
        }
        else if(body.classList.contains('clr-3') | body.classList.contains('bs-clr-3')) {
            clr = 3        
        }
        else { //if(body.classList.contains('clr-4') | body.classList.contains('bs-clr-4')) {
            clr = 4        
        }
        // cambiando valore input
        document.querySelector('#theme_color').value = clr

        body.classList.replace('bs-clr-'+clr, 'bs-clr-'+new_color)
        body.classList.replace('clr-'+clr, 'clr-'+new_color)
        // selezione
        document.querySelectorAll('#colors_list>.selector_entry').forEach(element => {element.classList.remove('selected')})
        document.querySelector('#colors_list>.selector_entry.se-'+new_color).classList.add('selected')
    } else {
        // cambiando valore input
        document.querySelector('#theme_color').value = -1

        document.querySelectorAll('#colors_list>.selector_entry').forEach(element => {element.classList.remove('selected')})
        document.querySelector('#colors_list>.selector_entry.se-random').classList.add('selected')
    }
}