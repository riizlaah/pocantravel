
async function loadAndAttachIcons() {
  const eyeRes = await fetch("/icons/eye.svg");
  const eyeOffRes = await fetch("/icons/eye-off.svg");
  const eyeSVG = await eyeRes.text();
  const eyeOffSVG = await eyeOffRes.text();
  attachIcons(eyeOffSVG, eyeSVG)
}

function attachIcons(eyeOff, eyeOn) {
  const passwordInputs = document.querySelectorAll("input[type=\"password\"]");
  passwordInputs.forEach(password => {
    const btn = password.parentElement.children[1];
    btn.innerHTML = eyeOn;
    // label > div > password
    //             > button > svg
    btn.onclick = () => {
      if (password.type === "password") {
        password.type = "text"
        btn.innerHTML = eyeOff;
      } else {
        password.type = "password"
        btn.innerHTML = eyeOn;
      }
    };

  });
}

loadAndAttachIcons();