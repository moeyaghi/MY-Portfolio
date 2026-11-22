document.addEventListener("DOMContentLoaded", function () {
  var menuButton = document.querySelector(".menu-toggle");
  var sidebar = document.querySelector(".sidebar");
  var sideLinks = document.querySelectorAll(".side-link");
  var taskbarLinks = document.querySelectorAll(".taskbar-app[data-section]");
  var filterButtons = document.querySelectorAll(".filter-button");
  var projectRows = document.querySelectorAll(".project-row");
  var clock = document.querySelector(".taskbar-clock");
  var date = document.querySelector(".taskbar-date");
  var themeButton = document.querySelector(".theme-toggle");
  var layoutButton = document.querySelector(".layout-toggle");
  var savedTheme = null;
  var savedLayout = null;
  var filterTimer = null;
  var prefersReducedMotion = window.matchMedia("(prefers-reduced-motion: reduce)").matches;

  function setActiveNavLink(activeLink) {
    sideLinks.forEach(function (link) {
      var isActive = link === activeLink;
      link.classList.toggle("active", isActive);
      if (isActive) {
        link.setAttribute("aria-current", "location");
      } else {
        link.removeAttribute("aria-current");
      }
    });

    taskbarLinks.forEach(function (item) {
      var isActive = item.dataset.section === activeLink.getAttribute("href").slice(1);
      item.classList.toggle("active", isActive);
      if (isActive) {
        item.setAttribute("aria-current", "location");
      } else {
        item.removeAttribute("aria-current");
      }
    });
  }

  try {
    savedTheme = window.localStorage.getItem("myaghi-concept-theme");
  } catch (error) {
    savedTheme = null;
  }

  function applyTheme(theme) {
    document.body.dataset.theme = theme;
    var isLight = theme === "light";
    if (themeButton) {
      themeButton.setAttribute("aria-pressed", String(isLight));
      themeButton.title = isLight ? "Switch to dark theme" : "Switch to light theme";
      themeButton.innerHTML = isLight
        ? '<i class="bi bi-moon-stars"></i><span>DARK UI</span>'
        : '<i class="bi bi-sun"></i><span>LIGHT UI</span>';
    }
    var themeColor = document.querySelector('meta[name="theme-color"]');
    if (themeColor) {
      themeColor.content = isLight ? "#eef2ef" : "#101619";
    }
  }

  if (savedTheme === "light" || savedTheme === "dark") {
    applyTheme(savedTheme);
  }

  if (themeButton) {
    themeButton.addEventListener("click", function () {
      var nextTheme = document.body.dataset.theme === "light" ? "dark" : "light";
      applyTheme(nextTheme);
      try {
        window.localStorage.setItem("myaghi-concept-theme", nextTheme);
      } catch (error) {}
    });
  }

  try {
    savedLayout = window.localStorage.getItem("myaghi-concept-layout");
  } catch (error) {
    savedLayout = null;
  }

  function applyLayout(layout) {
    var isOptimized = layout === "optimized";
    document.body.dataset.layout = isOptimized ? "optimized" : "normal";
    if (layoutButton) {
      layoutButton.setAttribute("aria-pressed", String(isOptimized));
      layoutButton.title = isOptimized ? "Switch to normal sizing" : "Switch to optimized sizing";
      layoutButton.innerHTML = isOptimized
        ? '<i class="bi bi-aspect-ratio"></i><span>OPTIMIZED</span>'
        : '<i class="bi bi-aspect-ratio"></i><span>NORMAL</span>';
    }
  }

  applyLayout(savedLayout === "normal" ? "normal" : "optimized");

  if (layoutButton) {
    layoutButton.addEventListener("click", function () {
      var nextLayout = document.body.dataset.layout === "optimized" ? "normal" : "optimized";
      applyLayout(nextLayout);
      try {
        window.localStorage.setItem("myaghi-concept-layout", nextLayout);
      } catch (error) {}
    });
  }

  if (menuButton && sidebar) {
    menuButton.addEventListener("click", function () {
      var isOpen = sidebar.classList.toggle("is-open");
      menuButton.setAttribute("aria-expanded", String(isOpen));
    });

    sideLinks.forEach(function (link) {
      link.addEventListener("click", function () {
        setActiveNavLink(link);
        sidebar.classList.remove("is-open");
        menuButton.setAttribute("aria-expanded", "false");
      });
    });

    taskbarLinks.forEach(function (item) {
      item.addEventListener("click", function (event) {
        var sideLink = Array.from(sideLinks).find(function (link) {
          return link.dataset.section === item.dataset.section;
        });
        var target = document.getElementById(item.dataset.section);
        if (sideLink && target) {
          event.preventDefault();
          window.history.pushState(null, "", item.getAttribute("href"));
          target.scrollIntoView({ behavior: "instant", block: "start" });
          setActiveNavLink(sideLink);
        }
      });
    });

    document.addEventListener("keydown", function (event) {
      if (event.key === "Escape" && sidebar.classList.contains("is-open")) {
        sidebar.classList.remove("is-open");
        menuButton.setAttribute("aria-expanded", "false");
        menuButton.focus();
      }
    });

    window.addEventListener("resize", function () {
      if (window.innerWidth > 900) {
        sidebar.classList.remove("is-open");
        menuButton.setAttribute("aria-expanded", "false");
      }
    });

    document.addEventListener("click", function (event) {
      if (!sidebar.contains(event.target) && !menuButton.contains(event.target)) {
        sidebar.classList.remove("is-open");
        menuButton.setAttribute("aria-expanded", "false");
      }
    });
  }

  var observedSections = Array.from(sideLinks).map(function (link) {
      var href = link.getAttribute("href");
      if (!href || href.charAt(0) !== "#") { return null; }
      var target = document.querySelector(href);
      return target ? { link: link, target: target } : null;
    }).filter(Boolean);

  function updateActiveSection() {
    if (!observedSections.length) { return; }
    var topbar = document.querySelector(".topbar");
    var checkpoint = window.scrollY + (topbar ? topbar.getBoundingClientRect().height : 54) + 56;
    var current = observedSections[0];
    var closestSectionTop = -Infinity;
    var closestSections = [];

    observedSections.forEach(function (item) {
      var sectionTop = item.target.getBoundingClientRect().top + window.scrollY;
      if (sectionTop > checkpoint) { return; }
      if (sectionTop > closestSectionTop + 4) {
        closestSectionTop = sectionTop;
        closestSections = [item];
      } else if (Math.abs(sectionTop - closestSectionTop) <= 4) {
        closestSections.push(item);
      }
    });
    if (closestSections.length) {
      var requestedSection = closestSections.find(function (item) {
        return item.target.id === window.location.hash.slice(1);
      });
      current = requestedSection || closestSections[0];
    }

    if (window.scrollY + window.innerHeight >= document.documentElement.scrollHeight - 4) {
      var bottomAnchor = observedSections.find(function (item) {
        return item.target.id === window.location.hash.slice(1);
      });
      var requestedDistance = bottomAnchor
        ? checkpoint - (bottomAnchor.target.getBoundingClientRect().top + window.scrollY)
        : Infinity;
      current = bottomAnchor && requestedDistance >= 0 && requestedDistance <= 100
        ? bottomAnchor
        : observedSections[observedSections.length - 1];
    }

    setActiveNavLink(current.link);
  }

  function updateActiveSectionFromHash() {
    var sectionId = window.location.hash.slice(1);
    var section = observedSections.find(function (item) {
      return item.target.id === sectionId;
    });
    if (section) {
      setActiveNavLink(section.link);
    } else {
      updateActiveSection();
    }
  }

  var activeSectionFrame = false;
  window.addEventListener("scroll", function () {
    if (activeSectionFrame) { return; }
    activeSectionFrame = true;
    window.requestAnimationFrame(function () {
      updateActiveSection();
      activeSectionFrame = false;
    });
  }, { passive: true });
  window.addEventListener("resize", updateActiveSection);
  window.addEventListener("hashchange", updateActiveSectionFromHash);
  window.addEventListener("pageshow", updateActiveSectionFromHash);
  window.addEventListener("load", updateActiveSectionFromHash, { once: true });
  window.requestAnimationFrame(updateActiveSectionFromHash);

  var revealTargets = document.querySelectorAll(
    ".workspace-heading, .hero-window, .metric-window, .capabilities-window, .experience-window, .projects-window, .contact-window"
  );

  var contactDialog = document.getElementById("contact-terminal");
  var contactLauncher = document.getElementById("open-contact-terminal");
  var contactTaskbar = document.getElementById("open-contact-taskbar");
  var contactClose = document.getElementById("close-contact-terminal");
  var contactForm = document.getElementById("contact-form");
  var securityDialog = document.getElementById("contact-security");
  var securityBackdrop = document.getElementById("contact-security-backdrop");
  var securityTaskbar = document.getElementById("open-security-taskbar");
  var securityClose = document.getElementById("cancel-contact-security");
  var securityCancel = document.getElementById("security-cancel-action");
  var securityRetry = document.getElementById("retry-contact-captcha");
  var captchaStatus = document.getElementById("contact-captcha-status");
  var captchaTokenField = document.getElementById("contact-captcha-token");
  var captchaEnabled = Boolean(contactForm && contactForm.dataset.captchaEnabled === "1");
  var terminalOutput = document.getElementById("terminal-output");
  var terminalPrompt = document.getElementById("terminal-prompt");
  var terminalEntry = document.getElementById("terminal-entry");
  var terminalModeCommand = "";
  var captchaScriptRequested = Boolean(window.grecaptcha && window.grecaptcha.enterprise);

  if (contactDialog && contactLauncher && contactForm && securityDialog && terminalOutput && terminalPrompt && terminalEntry && captchaTokenField) {
    var terminalValues = { name: "", email: "", message: "" };
    var terminalStep = 0;
    var terminalPhase = "collect";
    var terminalSending = false;
    var terminalHasSession = false;
    var editTarget = 0;
    var editReturnStep = 0;
    var backTarget = 0;
    var terminalPrompts = [
      { key: "name", command: "set /p NAME=Name:", editCommand: "set /p NAME=Update name:", label: "Your name, then press Enter", placeholder: "Type your name here, then press Enter", editPlaceholder: "Update the name, then press Enter", maxLength: 120, autocomplete: "name" },
      { key: "email", command: "set /p EMAIL=Email:", editCommand: "set /p EMAIL=Update email:", label: "Your email, then press Enter", placeholder: "Type your email here, then press Enter", editPlaceholder: "Update the email, then press Enter", maxLength: 254, autocomplete: "email" },
      { key: "message", command: "set /p MESSAGE=Message:", editCommand: "set /p MESSAGE=Update message:", label: "Your message, press Ctrl plus Enter for a new line, then Enter to send", placeholder: "Write message; Ctrl+Enter for a new line", editPlaceholder: "Update message; Ctrl+Enter for a new line", maxLength: 5000, autocomplete: "off" }
    ];

    function openSecurityDialog() {
      if (!securityDialog.open) { securityDialog.show(); }
      if (securityBackdrop) { securityBackdrop.hidden = false; }
    }

    function closeSecurityDialog() {
      if (securityDialog.open) { securityDialog.close(); }
      if (securityBackdrop) { securityBackdrop.hidden = true; }
    }

    function appendTerminalLine(text, className) {
      var line = document.createElement("p");
      line.className = "terminal-added " + (className || "terminal-response");
      line.textContent = text;
      terminalOutput.appendChild(line);
      terminalOutput.scrollTop = terminalOutput.scrollHeight;
      return line;
    }

    function setTerminalPrompt(prefill, focusField) {
      var prompt = terminalPrompts[terminalStep];
      var isEditing = terminalPhase === "editing";
      terminalPrompt.textContent = "C:\\Users\\visitor> " + (isEditing ? prompt.editCommand : prompt.command);
      terminalEntry.value = prefill ? terminalValues[prompt.key] : "";
      terminalEntry.maxLength = prompt.maxLength;
      terminalEntry.autocomplete = prompt.autocomplete;
      terminalEntry.setAttribute("aria-label", prompt.label);
      terminalEntry.placeholder = isEditing ? prompt.editPlaceholder : prompt.placeholder;
      terminalEntry.rows = terminalStep === 2 ? 2 : 1;
      if (focusField !== false) { terminalEntry.focus({ preventScroll: true }); }
      if (prefill) { terminalEntry.select(); }
    }

    function setModePrompt(command, placeholder, label) {
      terminalModeCommand = command;
      terminalPrompt.textContent = "C:\\Users\\visitor> " + command;
      terminalEntry.value = "";
      terminalEntry.maxLength = 80;
      terminalEntry.autocomplete = "off";
      terminalEntry.setAttribute("aria-label", label || command);
      terminalEntry.placeholder = placeholder;
      terminalEntry.rows = 1;
      terminalEntry.disabled = false;
      terminalPrompt.parentElement.hidden = false;
      if (!terminalEntry.disabled && !terminalPrompt.parentElement.hidden) {
        terminalEntry.focus({ preventScroll: true });
      }
    }

    function resetCaptchaWidget() {
      captchaTokenField.value = "";
      if (window.grecaptcha && window.grecaptcha.enterprise) {
        try { window.grecaptcha.enterprise.reset(); } catch (error) {}
      }
    }

    function openSecurityCheck() {
      terminalPhase = "captcha";
      resetCaptchaWidget();
      if (securityTaskbar) {
        securityTaskbar.hidden = false;
        securityTaskbar.classList.add("active");
        securityTaskbar.setAttribute("aria-expanded", "true");
      }
      if (captchaStatus) {
        captchaStatus.textContent = window.grecaptcha && window.grecaptcha.enterprise
          ? "Complete the security check to continue."
          : "Loading security check...";
      }
      openSecurityDialog();
      if (!captchaScriptRequested) {
        captchaScriptRequested = true;
        var captchaScript = document.createElement("script");
        captchaScript.src = "https://www.google.com/recaptcha/enterprise.js";
        captchaScript.async = true;
        captchaScript.defer = true;
        captchaScript.addEventListener("load", function () {
          if (securityDialog.open && captchaStatus) {
            captchaStatus.textContent = "Complete the security check to continue.";
          }
        });
        captchaScript.addEventListener("error", function () {
          if (securityDialog.open && captchaStatus) {
            captchaStatus.textContent = "Security service unavailable. Retry or cancel this check.";
          }
        });
        document.head.appendChild(captchaScript);
      }
      window.setTimeout(function () {
        if (securityDialog.open && !(window.grecaptcha && window.grecaptcha.enterprise) && captchaStatus) {
          captchaStatus.textContent = "Security service unavailable. Retry or cancel this check.";
        }
      }, 7000);
    }

    function appendModeAnswer(value) {
      appendTerminalLine("C:\\Users\\visitor> " + terminalModeCommand + " " + value, "terminal-command-history");
    }

    function beginEditing(target) {
      if (terminalValues[terminalPrompts[target].key] === "") {
        appendTerminalLine("No saved " + terminalPrompts[target].key + " to edit.", "terminal-error");
        terminalPhase = "collect";
        setTerminalPrompt();
        return;
      }
      editReturnStep = terminalStep;
      editTarget = target;
      terminalStep = target;
      terminalPhase = "editing";
      setTerminalPrompt(true);
    }

    function askToTryAgain() {
      terminalPhase = "after-send";
      setModePrompt("Try again? [Y/N/EXIT]", "Type Y to start again, N or EXIT to close", "Try again? Y, N, or EXIT");
    }

    function submitContactMessage() {
      if (terminalSending) { return; }
      terminalSending = true;
      terminalPhase = "sending";
      terminalEntry.disabled = true;
      terminalPrompt.parentElement.hidden = true;
      var pendingLine = appendTerminalLine(captchaEnabled ? "Verifying security and sending..." : "Sending...", "terminal-pending");

      fetch(contactForm.action, {
        method: "POST",
        body: new FormData(contactForm),
        headers: { Accept: "application/json" }
      }).then(function (response) {
        return response.json();
      }).then(function (result) {
        pendingLine.remove();

        if (result.captchaRequired) {
          captchaTokenField.value = "";
          appendTerminalLine("Security check was not accepted. Type RETRY, EDIT, or EXIT.", "terminal-error");
          terminalPhase = "captcha-cancelled";
          terminalEntry.disabled = false;
          setModePrompt("Security check rejected. [RETRY/EDIT/EXIT]", "Type RETRY, EDIT, or EXIT", "Choose retry, edit, or exit");
          return;
        }

        if (result.configurationRequired && result.recipient) {
          appendTerminalLine("Mail delivery is not configured. You can use this command:");
          var subject = "Portfolio contact from " + terminalValues.name;
          var bodyText = "Name: " + terminalValues.name + "\nEmail: " + terminalValues.email + "\n\nMessage:\n" + terminalValues.message;
          var fallback = document.createElement("a");
          fallback.className = "terminal-added terminal-mailto";
          fallback.href = "mailto:" + result.recipient + "?subject=" + encodeURIComponent(subject) + "&body=" + encodeURIComponent(bodyText);
          fallback.textContent = "C:\\Users\\visitor> start mailto:" + result.recipient;
          terminalOutput.appendChild(fallback);
          askToTryAgain();
        } else if (result.ok) {
          appendTerminalLine("Message sent successfully.", "terminal-success");
          askToTryAgain();
        } else {
          appendTerminalLine(result.message || "The message could not be sent.", "terminal-error");
          terminalPhase = "collect";
          terminalStep = 2;
          terminalEntry.disabled = false;
          resetCaptchaWidget();
          setTerminalPrompt(true);
        }
      }).catch(function () {
        pendingLine.remove();
        appendTerminalLine("The command could not be completed.", "terminal-error");
        terminalPhase = "collect";
        terminalStep = 2;
        terminalEntry.disabled = false;
        resetCaptchaWidget();
        setTerminalPrompt(true);
      }).finally(function () {
        terminalSending = false;
      });
    }

    function processTerminalInput(rawValue) {
      var value = rawValue.trim();
      var normalized = value.toLowerCase();

      if (terminalPhase === "edit-confirm") {
        appendModeAnswer(value);
        if (normalized === "y" || normalized === "yes") {
          beginEditing(backTarget);
        } else if (normalized === "n" || normalized === "no") {
          terminalPhase = "collect";
          setTerminalPrompt(terminalValues[terminalPrompts[terminalStep].key] !== "");
        } else {
          appendTerminalLine("Enter Y or N.", "terminal-error");
          setModePrompt("Edit the previous answer? [Y/N]", "Type Y or N, then press Enter", "Edit the previous answer?");
        }
        return;
      }

      if (terminalPhase === "captcha-cancelled") {
        appendModeAnswer(value);
        if (normalized === "y" || normalized === "yes" || normalized === "r" || normalized === "retry") {
          openSecurityCheck();
        } else if (normalized === "edit") {
          editReturnStep = 2;
          editTarget = 2;
          terminalStep = 2;
          terminalPhase = "editing";
          setTerminalPrompt(true);
        } else if (normalized === "exit") {
          terminalDialogClose();
        } else {
          appendTerminalLine("Enter RETRY, EDIT, or EXIT.", "terminal-error");
          setModePrompt("Security check rejected. [RETRY/EDIT/EXIT]", "Type RETRY, EDIT, or EXIT", "Choose retry, edit, or exit");
        }
        return;
      }

      if (terminalPhase === "after-send") {
        appendModeAnswer(value);
        if (normalized === "y" || normalized === "yes") {
          terminalValues = { name: "", email: "", message: "" };
          contactForm.elements.namedItem("name").value = "";
          contactForm.elements.namedItem("email").value = "";
          contactForm.elements.namedItem("message").value = "";
          resetCaptchaWidget();
          terminalPhase = "collect";
          terminalStep = 0;
          terminalEntry.disabled = false;
          appendTerminalLine("Starting a new contact session...");
          setTerminalPrompt();
        } else if (normalized === "n" || normalized === "no" || normalized === "exit") {
          terminalDialogClose();
        } else {
          appendTerminalLine("Enter Y, N, or EXIT.", "terminal-error");
          setModePrompt("Try again? [Y/N/EXIT]", "Type Y, N, or EXIT", "Try again?");
        }
        return;
      }

      if (terminalPhase === "editing") {
        var editPrompt = terminalPrompts[terminalStep];
        if (value === "") {
          appendTerminalLine(editPrompt.key + " cannot be blank.", "terminal-error");
          return;
        }
        if (editPrompt.key === "email" && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value)) {
          appendTerminalLine("The email address format is invalid.", "terminal-error");
          terminalEntry.value = "";
          return;
        }
        terminalValues[editPrompt.key] = value;
        contactForm.elements.namedItem(editPrompt.key).value = value;
        appendTerminalLine("C:\\Users\\visitor> " + editPrompt.editCommand + value, "terminal-command-history");
        terminalPhase = "collect";
        terminalStep = editReturnStep;
        setTerminalPrompt(terminalValues[terminalPrompts[terminalStep].key] !== "");
        return;
      }

      if (terminalPhase !== "collect") { return; }

      if (/^help(?:\s+contact)?$/i.test(value)) {
        appendTerminalLine("C:\\Users\\visitor> " + value, "terminal-command-history");
        appendTerminalLine("EDIT NAME, EDIT EMAIL, EDIT MESSAGE - correct a saved answer.");
        appendTerminalLine("At a blank prompt, press Backspace to edit the previous answer.");
        appendTerminalLine("After security check, sending starts automatically. Afterward: Y retries, N finishes, EXIT closes.");
        setTerminalPrompt(terminalValues[terminalPrompts[terminalStep].key] !== "");
        return;
      }

      var requestedEdit = /^edit\s+(name|email|message)$/i.exec(value);
      if (requestedEdit) {
        var requestedIndex = terminalPrompts.findIndex(function (prompt) { return prompt.key === requestedEdit[1].toLowerCase(); });
        appendTerminalLine("C:\\Users\\visitor> " + value, "terminal-command-history");
        beginEditing(requestedIndex);
        return;
      }

      var prompt = terminalPrompts[terminalStep];
      if (value === "") {
        appendTerminalLine(prompt.key + " cannot be blank.", "terminal-error");
        return;
      }
      if (prompt.key === "email" && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value)) {
        appendTerminalLine("C:\\Users\\visitor> " + prompt.command + value, "terminal-command-history");
        appendTerminalLine("The email address format is invalid.", "terminal-error");
        terminalEntry.value = "";
        return;
      }

      terminalValues[prompt.key] = value;
      contactForm.elements.namedItem(prompt.key).value = value;
      appendTerminalLine("C:\\Users\\visitor> " + prompt.command + value, "terminal-command-history");

      if (terminalStep < terminalPrompts.length - 1) {
        terminalStep++;
        setTerminalPrompt();
      } else {
        terminalEntry.value = "";
        terminalEntry.disabled = true;
        terminalPrompt.parentElement.hidden = true;
        if (captchaEnabled) { openSecurityCheck(); }
        else { submitContactMessage(); }
      }
    }

    function askToEditPrevious() {
      backTarget = terminalStep - 1;
      terminalPhase = "edit-confirm";
      setModePrompt("Edit the previous answer? [Y/N]", "Type Y to edit or N to continue", "Edit the previous answer?");
    }

    function cancelSecurityCheck() {
      resetCaptchaWidget();
      closeSecurityDialog();
      resetWindowMaximize("security");
      if (securityTaskbar) {
        securityTaskbar.hidden = true;
        securityTaskbar.classList.remove("active");
      }
      appendTerminalLine("Security check cancelled.");
      terminalPhase = "captcha-cancelled";
      setModePrompt("Retry security check? [Y/EDIT/EXIT]", "Type Y, EDIT, or EXIT", "Choose retry, edit, or exit");
    }

    function minimizeSecurityCheck() {
      closeSecurityDialog();
      if (securityTaskbar) {
        securityTaskbar.hidden = false;
        securityTaskbar.classList.add("active");
        securityTaskbar.setAttribute("aria-expanded", "false");
      }
    }

    function minimizeTerminal() {
      if (contactDialog.open) { contactDialog.close(); }
      if (contactTaskbar) {
        contactTaskbar.hidden = false;
        contactTaskbar.classList.add("active");
        contactTaskbar.setAttribute("aria-expanded", "false");
        contactTaskbar.setAttribute("aria-label", "Restore contact terminal");
        contactTaskbar.title = "Restore contact terminal";
      }
    }

    function resetWindowMaximize(windowName) {
      var dialog = windowName === "security" ? securityDialog : contactDialog;
      var button = document.querySelector('[data-maximize-window="' + windowName + '"]');
      dialog.classList.remove("is-maximized");
      dialog.style.removeProperty("--window-offset-x");
      dialog.style.removeProperty("--window-offset-y");
      if (button) {
        button.setAttribute("aria-pressed", "false");
        button.title = "Maximize";
        button.innerHTML = '<i class="bi bi-square"></i>';
      }
    }

    function restoreTerminal() {
      if (!terminalHasSession) {
        openTerminal();
        return;
      }
      if (!contactDialog.open) { contactDialog.show(); }
      if (contactTaskbar) {
        contactTaskbar.hidden = false;
        contactTaskbar.classList.add("active");
        contactTaskbar.setAttribute("aria-expanded", "true");
        contactTaskbar.setAttribute("aria-label", "Minimize contact terminal");
        contactTaskbar.title = "Minimize contact terminal";
      }
      if (!terminalEntry.disabled && !terminalPrompt.parentElement.hidden) {
        terminalEntry.focus({ preventScroll: true });
      }
    }

    function terminalDialogClose() {
      closeSecurityDialog();
      if (securityTaskbar) {
        securityTaskbar.hidden = true;
        securityTaskbar.classList.remove("active");
        securityTaskbar.setAttribute("aria-expanded", "false");
      }
      if (contactDialog.open) { contactDialog.close(); }
      resetWindowMaximize("contact");
      resetWindowMaximize("security");
      terminalHasSession = false;
      if (contactTaskbar) {
        contactTaskbar.hidden = true;
        contactTaskbar.classList.remove("active");
        contactTaskbar.setAttribute("aria-expanded", "false");
        contactTaskbar.setAttribute("aria-label", "Restore contact terminal");
        contactTaskbar.title = "Open contact terminal";
      }
      resetTerminal();
    }

    function toggleWindowMaximize(windowName, button) {
      var dialog = windowName === "security" ? securityDialog : contactDialog;
      var maximized = dialog.classList.toggle("is-maximized");
      button.setAttribute("aria-pressed", String(maximized));
      button.title = maximized ? "Restore" : "Maximize";
      button.innerHTML = maximized ? '<i class="bi bi-fullscreen-exit"></i>' : '<i class="bi bi-square"></i>';
    }

    function makeWindowDraggable(dialog, titlebar, windowName) {
      if (!titlebar) { return; }
      titlebar.addEventListener("pointerdown", function (event) {
        if (event.button !== 0 || event.target.closest("button") || dialog.classList.contains("is-maximized")) { return; }
        event.preventDefault();
        var rect = dialog.getBoundingClientRect();
        var startX = event.clientX;
        var startY = event.clientY;
        var topbarHeight = document.querySelector(".topbar").getBoundingClientRect().height;
        var taskbar = document.querySelector(".taskbar");
        var taskbarHeight = taskbar ? taskbar.getBoundingClientRect().height : 42;
        var baseLeft = windowName === "contact" && window.innerWidth <= 560 ? 10 : (window.innerWidth - rect.width) / 2;
        var baseTop = windowName === "security"
          ? topbarHeight + 12 + (window.innerHeight - topbarHeight - taskbarHeight - 24 - rect.height) / 2
          : topbarHeight + (window.innerHeight - topbarHeight - taskbarHeight) / 2 - rect.height / 2;

        function moveWindow(moveEvent) {
          var minTop = topbarHeight + 4;
          var maxLeft = Math.max(4, window.innerWidth - rect.width - 4);
          var maxTop = Math.max(minTop, window.innerHeight - taskbarHeight - rect.height - 4);
          var left = Math.max(4, Math.min(maxLeft, rect.left + moveEvent.clientX - startX));
          var top = Math.max(minTop, Math.min(maxTop, rect.top + moveEvent.clientY - startY));
          dialog.style.setProperty("--window-offset-x", (left - baseLeft) + "px");
          dialog.style.setProperty("--window-offset-y", (top - baseTop) + "px");
        }

        function stopMoving() {
          titlebar.removeEventListener("pointermove", moveWindow);
          titlebar.removeEventListener("pointerup", stopMoving);
          titlebar.removeEventListener("pointercancel", stopMoving);
          if (titlebar.hasPointerCapture(event.pointerId)) { titlebar.releasePointerCapture(event.pointerId); }
        }

        titlebar.setPointerCapture(event.pointerId);
        titlebar.addEventListener("pointermove", moveWindow);
        titlebar.addEventListener("pointerup", stopMoving);
        titlebar.addEventListener("pointercancel", stopMoving);
      });
    }

    makeWindowDraggable(contactDialog, contactDialog.querySelector(".terminal-titlebar"), "contact");
    makeWindowDraggable(securityDialog, securityDialog.querySelector(".security-titlebar"), "security");

    window.onContactCaptchaVerified = function (token) {
      if (!token || terminalPhase !== "captcha") { return; }
      captchaTokenField.value = token;
      closeSecurityDialog();
      resetWindowMaximize("security");
      if (securityTaskbar) {
        securityTaskbar.hidden = true;
        securityTaskbar.classList.remove("active");
        securityTaskbar.setAttribute("aria-expanded", "false");
      }
      submitContactMessage();
    };

    window.onContactCaptchaExpired = function () {
      captchaTokenField.value = "";
      if (captchaStatus) { captchaStatus.textContent = "Security check expired. Please verify again."; }
    };

    window.onContactCaptchaError = function () {
      if (captchaStatus) { captchaStatus.textContent = "Security check could not load. Retry or cancel."; }
    };

    function resetTerminal() {
      terminalOutput.querySelectorAll(".terminal-added").forEach(function (line) { line.remove(); });
      terminalValues = { name: "", email: "", message: "" };
      terminalStep = 0;
      terminalPhase = "collect";
      terminalSending = false;
      contactForm.elements.namedItem("name").value = "";
      contactForm.elements.namedItem("email").value = "";
      contactForm.elements.namedItem("message").value = "";
      resetCaptchaWidget();
      resetWindowMaximize("security");
      if (securityTaskbar) {
        securityTaskbar.hidden = true;
        securityTaskbar.classList.remove("active");
      }
      terminalPrompt.parentElement.hidden = false;
      terminalEntry.disabled = false;
      resetWindowMaximize("contact");
      setTerminalPrompt(false, false);
    }

    function openTerminal() {
      if (terminalHasSession) { restoreTerminal(); return; }
      resetTerminal();
      terminalHasSession = true;
      contactDialog.show();
      setTerminalPrompt();
      if (contactTaskbar) {
        contactTaskbar.hidden = false;
        contactTaskbar.classList.add("active");
        contactTaskbar.setAttribute("aria-expanded", "true");
        contactTaskbar.setAttribute("aria-label", "Minimize contact terminal");
        contactTaskbar.title = "Minimize contact terminal";
      }
    }

    contactLauncher.addEventListener("click", function (event) {
      event.preventDefault();
      openTerminal();
    });

    if (contactTaskbar) {
      contactTaskbar.hidden = true;
      contactTaskbar.setAttribute("aria-expanded", "false");
      contactTaskbar.addEventListener("click", function () {
        if (contactDialog.open) { minimizeTerminal(); }
        else { restoreTerminal(); }
      });
    }
    if (securityTaskbar) {
      securityTaskbar.hidden = true;
      securityTaskbar.setAttribute("aria-expanded", "false");
      securityTaskbar.addEventListener("click", function () {
        if (terminalPhase === "captcha" && !securityDialog.open) {
          openSecurityDialog();
          securityTaskbar.classList.add("active");
          securityTaskbar.setAttribute("aria-expanded", "true");
          if (captchaStatus) { captchaStatus.textContent = "Complete the security check to continue."; }
        }
      });
    }

    if (contactClose) {
      contactClose.addEventListener("click", terminalDialogClose);
    }

    contactDialog.addEventListener("cancel", function (event) {
      event.preventDefault();
      minimizeTerminal();
    });

    securityDialog.addEventListener("cancel", function (event) {
      event.preventDefault();
      minimizeSecurityCheck();
    });

    securityDialog.addEventListener("click", function (event) {
      if (event.target === securityDialog) { minimizeSecurityCheck(); }
    });

    if (securityCancel) { securityCancel.addEventListener("click", cancelSecurityCheck); }
    if (securityClose) { securityClose.addEventListener("click", cancelSecurityCheck); }
    if (securityBackdrop) { securityBackdrop.addEventListener("click", minimizeSecurityCheck); }
    if (securityRetry) {
      securityRetry.addEventListener("click", function () {
        if (captchaStatus) { captchaStatus.textContent = "Retrying security check..."; }
        resetCaptchaWidget();
      });
    }

    document.querySelectorAll("[data-minimize-window]").forEach(function (button) {
      button.addEventListener("click", function () {
        if (button.dataset.minimizeWindow === "security") { minimizeSecurityCheck(); }
        else { minimizeTerminal(); }
      });
    });
    document.querySelectorAll("[data-maximize-window]").forEach(function (button) {
      button.addEventListener("click", function () {
        toggleWindowMaximize(button.dataset.maximizeWindow, button);
      });
    });

    document.addEventListener("pointerdown", function (event) {
      if (contactDialog.open && !securityDialog.open && !contactDialog.contains(event.target)
        && !event.target.closest("#open-contact-taskbar") && !event.target.closest("#open-contact-terminal")
        && !event.target.closest("#open-security-taskbar")) {
        minimizeTerminal();
      }
    });

    terminalEntry.addEventListener("input", function () {
      var isMessageEntry = terminalStep === 2 && (terminalPhase === "collect" || terminalPhase === "editing");
      terminalEntry.rows = isMessageEntry ? Math.min(5, Math.max(2, terminalEntry.value.split("\n").length)) : 1;
    });

    terminalEntry.addEventListener("keydown", function (event) {
      if (event.key === "Backspace" && terminalEntry.value === "" && terminalPhase === "collect" && terminalStep > 0) {
        event.preventDefault();
        askToEditPrevious();
        return;
      }
      if (event.key === "Enter" && (event.ctrlKey || event.metaKey) && terminalStep === 2
        && (terminalPhase === "collect" || terminalPhase === "editing")) {
        event.preventDefault();
        var selectionStart = terminalEntry.selectionStart;
        var selectionEnd = terminalEntry.selectionEnd;
        terminalEntry.value = terminalEntry.value.slice(0, selectionStart) + "\n" + terminalEntry.value.slice(selectionEnd);
        terminalEntry.selectionStart = terminalEntry.selectionEnd = selectionStart + 1;
        terminalEntry.dispatchEvent(new Event("input", { bubbles: true }));
        return;
      }
      if (event.key !== "Enter") { return; }
      if (event.shiftKey && terminalStep === 2 && (terminalPhase === "collect" || terminalPhase === "editing")) { return; }
      event.preventDefault();
      processTerminalInput(terminalEntry.value);
    });
    }

  if (!prefersReducedMotion && "IntersectionObserver" in window) {
    var revealObserver = new IntersectionObserver(function (entries) {
      entries.forEach(function (entry) {
        if (entry.isIntersecting) {
          entry.target.classList.add("is-visible");
          revealObserver.unobserve(entry.target);
        }
      });
    }, { threshold: 0.12, rootMargin: "0px 0px -6% 0px" });

    var metricRevealIndex = 0;
    revealTargets.forEach(function (target) {
      target.classList.add("reveal-on-scroll");
      if (target.classList.contains("metric-window")) {
        target.style.setProperty("--reveal-delay", metricRevealIndex * 65 + "ms");
        metricRevealIndex++;
      }
      revealObserver.observe(target);
    });
  }

  filterButtons.forEach(function (button) {
    button.addEventListener("click", function () {
      var filter = button.dataset.filter;
      filterButtons.forEach(function (item) {
        var isActive = item === button;
        item.classList.toggle("active", isActive);
        item.setAttribute("aria-pressed", String(isActive));
      });

      if (filterTimer !== null) {
        window.clearTimeout(filterTimer);
      }

      projectRows.forEach(function (project) { project.classList.add("is-filtering"); });
      filterTimer = window.setTimeout(function () {
        var enteringRows = [];
        projectRows.forEach(function (project) {
          var categories = (project.dataset.category || "").split(/\s+/);
          var matchesFilter = filter === "all" || categories.includes(filter);
          project.classList.toggle("is-hidden", !matchesFilter);
          if (matchesFilter) {
            enteringRows.push(project);
          } else {
            project.classList.remove("is-filtering");
          }
        });
        window.requestAnimationFrame(function () {
          enteringRows.forEach(function (project) { project.classList.remove("is-filtering"); });
        });
        filterTimer = null;
      }, prefersReducedMotion ? 0 : 180);
    });
  });

  function updateClock() {
    var now = new Date();
    if (clock) {
      clock.textContent = new Intl.DateTimeFormat(undefined, { hour: "2-digit", minute: "2-digit" }).format(now);
    }
    if (date) {
      date.textContent = new Intl.DateTimeFormat(undefined, { day: "2-digit", month: "short", year: "numeric" }).format(now);
    }
  }

  updateClock();
  window.setInterval(updateClock, 30000);
});
