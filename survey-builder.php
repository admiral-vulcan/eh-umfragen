<div id="builder">
    <h3><?php echo translate('Neue Umfrage', 'de', $GLOBALS['lang']); ?></h3>

    <!-- Title -->
    <label for="title"><?php echo translate('Umfragetitel', 'de', $GLOBALS['lang']); ?></label>
    <input type="text" name="title" id="title" placeholder="<?php echo translate('mögl. ein bis zwei Wörter', 'de', $GLOBALS['lang']); ?>">

    <!-- Subtitle -->
    <label for="subtitle"><?php echo translate('Untertitel (empfohlen)', 'de', $GLOBALS['lang']); ?></label>
    <input type="text" name="subtitle" id="subtitle" placeholder="<?php echo translate('prägnant, mögl. bis zu zehn Wörter', 'de', $GLOBALS['lang']); ?>">

    <!-- Description -->
    <label for="description"><?php echo translate('Umfragebeschreibung', 'de', $GLOBALS['lang']); ?></label>
    <input type="text"  name="description" id="description" placeholder="<?php echo translate('Sinn und Zweck der Umfrage', 'de', $GLOBALS['lang']); ?>">

    <!-- Further Description -->
    <label for="further_description"><?php echo translate('Weiterführende Beschreibung (optional)', 'de', $GLOBALS['lang']); ?></label>
    <input type="text"  name="further_description" id="further_description" placeholder="<?php echo translate('Falls eine zweite Zeile notwendig ist', 'de', $GLOBALS['lang']); ?>">

    <!-- Additional contributors -->
    <label for="contributors"><?php echo translate('Weitere Mitwirkende (durch Kommata getrennt)', 'de', $GLOBALS['lang']); ?></label>
    <input type="text" name="contributors" id="contributors" placeholder="<?php echo translate('Vor und Nachnamen oder E-Mail-Adressen', 'de', $GLOBALS['lang']); ?>">

    <!-- Target group -->
    <label for="target_group"><?php echo translate('Anvisierte Zielgruppe', 'de', $GLOBALS['lang']); ?></label>
    <select name="target_group" id="target_group">
        <option value="" disabled><?php echo translate('Zielgruppe', 'de', $GLOBALS['lang']); ?></option>
        <option value="students"><?php echo translate('Studierende der EH', 'de', $GLOBALS['lang']); ?></option>
        <option value="lecturers"><?php echo translate('Dozierende und Mitarbeitende der EH', 'de', $GLOBALS['lang']); ?></option>
        <option value="students&lecturers"><?php echo translate('Alle an der EH', 'de', $GLOBALS['lang']); ?></option>
        <option value="no_restriction"><?php echo translate('ohne Einschränkung', 'de', $GLOBALS['lang']); ?></option>
        <option value="other"><?php echo translate('Andere Zielgruppe', 'de', $GLOBALS['lang']); ?></option>
    </select>
    <label for="email_domain" id="email_domain_label" style="display: none;"><?php echo translate('E-Mail-Domains (durch Kommata getrennt)', 'de', $GLOBALS['lang']); ?></label><input type="text" name="email_domain" id="email_domain" placeholder="<?php echo translate('Z.B.', 'de', $GLOBALS['lang']); ?> @ph-ludwigsburg.de, @uni-stuttgart.de" style="display: none;">

    <!-- Questions container -->
    <div id="questions-container"></div>

    <!-- Question type dropdown -->
    <label for="question_type"><?php echo translate('Neues Element:', 'de', $GLOBALS['lang']); ?></label>
    <select name="question_type" id="question_type">
        <option value="" disabled><?php echo translate('Elementtyp', 'de', $GLOBALS['lang']); ?></option>
        <option value="description"><?php echo translate('Beschreibender Text', 'de', $GLOBALS['lang']); ?></option>
        <option value="free_text"><?php echo translate('Freie Texteingabe', 'de', $GLOBALS['lang']); ?></option>
        <option value="picture"><?php echo translate('Bild', 'de', $GLOBALS['lang']); ?></option>
        <option value="single_choice"><?php echo translate('Frage mit Einfachauswahl (nebeneinander)', 'de', $GLOBALS['lang']); ?></option>
        <option value="multiple_choice"><?php echo translate('Frage mit Mehrfachauswahl (nebeneinander)', 'de', $GLOBALS['lang']); ?></option>
        <option value="dropdown"><?php echo translate('Frage mit Mehrfachauswahl (untereinander in einem Menü)', 'de', $GLOBALS['lang']); ?></option>
    </select>

    <!-- Add question button -->
    <button type="button" id="add-question"><?php echo translate('Element hinzufügen', 'de', $GLOBALS['lang']); ?></button>
</div>

<?php
$followUpInfo = alert("Follow-Up-Element", "
    Ein Follow-Up-Element ist ein Element, das in der Umfrage erst auftaucht, 
    <br>wenn die vorherige Frage beantwortet wurde. Du kannst diese Funktion nutzen, 
    <br>um die befragte Person durch eine neue Frage nicht zu beeinflussen,  
    <br>oder falls eine weiterführende Frage nur dann sinnvoll ist, 
    <br>wenn die vorherige auch wirklich beantwortet wurde. 
    ", "info", false);
?>

<script type="application/javascript">
    const followUpInfoID = <?php echo $followUpInfo; ?>;
    const questionTypeSelect = document.getElementById("question_type");
    const addQuestionBtn = document.getElementById("add-question");
    const questionsContainer = document.getElementById("questions-container");
    const targetGroupSelect = document.getElementById("target_group");
    const emailDomainInput = document.getElementById("email_domain");
    const emailDomainLabel = document.getElementById("email_domain_label");
    const undoBtn = document.getElementById("button_undo");
    const redoBtn = document.getElementById("button_redo");
    let questionCount = 0;

    // Undo/redo stacks
    let undoStack = [];
    let redoStack = [];

    document.addEventListener("DOMContentLoaded", async () => {
        try {
            // Command class and specific command classes
            class Command {
                execute() { }
                unexecute() { }
            }

            class AddQuestionCommand extends Command {
                constructor(questionWrapper, questionsContainer) {
                    super();
                    this.questionWrapper = questionWrapper;
                    this.questionsContainer = questionsContainer;
                }

                execute() {
                    this.questionsContainer.appendChild(this.questionWrapper);
                }

                unexecute() {
                    this.questionsContainer.removeChild(this.questionWrapper);
                }
            }

            class DeleteQuestionCommand extends Command {
                constructor(questionWrapper, questionsContainer) {
                    super();
                    this.questionWrapper = questionWrapper;
                    this.questionsContainer = questionsContainer;
                    this.nextSibling = null;
                }

                execute() {
                    this.nextSibling = this.questionWrapper.nextElementSibling;
                    this.questionsContainer.removeChild(this.questionWrapper);
                }

                unexecute() {
                    this.questionsContainer.insertBefore(this.questionWrapper, this.nextSibling);
                }
            }


            class MoveQuestionCommand extends Command {
                constructor(questionWrapper, questionsContainer, direction) {
                    super();
                    this.questionWrapper = questionWrapper;
                    this.questionsContainer = questionsContainer;
                    this.direction = direction;
                    this.sibling = direction === "up" ? questionWrapper.previousElementSibling : questionWrapper.nextElementSibling;
                }

                execute() {
                    if (this.sibling) {
                        this.questionsContainer.insertBefore(this.questionWrapper, this.direction === "up" ? this.sibling : this.sibling.nextElementSibling);
                    }
                    updateQuestionNumbers();
                }

                unexecute() {
                    if (this.sibling) {
                        this.questionsContainer.insertBefore(this.questionWrapper, this.direction === "up" ? this.sibling.nextElementSibling : this.sibling);
                    }
                    updateQuestionNumbers();
                }
            }

            // Show email domain input if 'other' is selected in the target group dropdown
            targetGroupSelect.addEventListener("change", () => {
                if (targetGroupSelect.value === "other") {
                    emailDomainInput.style.display = "inline";
                    emailDomainLabel.style.display = "inline";
                } else {
                    emailDomainInput.style.display = "none";
                    emailDomainLabel.style.display = "none";
                }
            });

            function clearRedoStack() {
                redoStack = [];
            }

            // Add question based on question type
            addQuestionBtn.addEventListener("click", async () => {
                const questionType = questionTypeSelect.value;
                if (!questionType) return;

                questionCount++;

                const questionWrapper = document.createElement("div");
                questionWrapper.className = "question-wrapper";
                questionWrapper.dataset.questionType = questionType;

                const questionLabel = document.createElement("label");
                questionLabel.innerText = await translate("Element", "de", userLang) + ` ${questionCount}: ` + await typeToReadableType(questionType);
                questionLabel.className = questionType;
                questionWrapper.appendChild(questionLabel);
                const lineBreak = document.createElement("br");
                questionWrapper.appendChild(lineBreak);

                const followUpCheckbox = document.createElement("input");
                followUpCheckbox.type = "checkbox";
                followUpCheckbox.name = `question_${questionCount}_follow_up`;
                followUpCheckbox.id = `question_${questionCount}_follow_up`;

                const followUpForm = document.createElement("form");
                followUpForm.className = "not-selectable";

                const followUpLabel = document.createElement("label");
                followUpLabel.htmlFor = `question_${questionCount}_follow_up`;
                followUpLabel.innerText = await translate(`Element ${questionCount} ist ein Follow-up-Element`, "de", userLang);

                const followUpAnchor = document.createElement("a");
                followUpAnchor.style.cursor = "pointer";
                followUpAnchor.style.verticalAlign = "middle";
                followUpAnchor.style.marginTop = "10em";
                followUpAnchor.setAttribute("onclick", "showAlert(followUpInfoID)");
                followUpAnchor.innerText = await translate("Was ist ein Follow-up-Element?", "de", userLang);

                if (questionCount < 2) {
                    followUpCheckbox.style.display = "none";
                    followUpForm.style.display = "none";
                    followUpLabel.style.display = "none";
                    followUpAnchor.style.display = "none";
                }

                followUpForm.appendChild(followUpCheckbox);
                followUpForm.appendChild(followUpLabel);
                followUpForm.appendChild(lineBreak);
                followUpForm.appendChild(followUpAnchor);

                switch (questionType) {
                    case "description":
                        const descriptionInput = document.createElement("input");
                        descriptionInput.type = "text";
                        descriptionInput.name = `question_${questionCount}_description`;
                        questionWrapper.appendChild(descriptionInput);
                        break;
                    case "free_text":
                        const freeTextInput = document.createElement("input");
                        freeTextInput.type = "text";
                        freeTextInput.name = `question_${questionCount}_free_text`;
                        questionWrapper.appendChild(freeTextInput);
                        break;
                    case "picture":
                        const fileInput = document.createElement("input");
                        fileInput.type = "file";
                        fileInput.accept = "image/*";
                        fileInput.name = `question_${questionCount}_picture`;
                        questionWrapper.appendChild(fileInput);
                        break;
                    case "multiple_choice":
                    case "single_choice":
                    case "dropdown":
                        const choiceType = questionType === "dropdown" ? "select" : "div";
                        const choiceContainer = document.createElement("div");
                        choiceContainer.className = "choice-container";
                        choiceContainer.dataset.choiceCount = 1;

                        const choiceWrapper = document.createElement(choiceType);
                        choiceWrapper.name = `question_${questionCount}_choices`;

                    async function addChoice(choiceContainer) {
                        const choiceCount = parseInt(choiceContainer.dataset.choiceCount, 10);
                        const newChoiceCount = choiceCount + 1;
                        choiceContainer.dataset.choiceCount = newChoiceCount;

                        const choiceInput = document.createElement("input");
                        choiceInput.type = "text";
                        choiceInput.name = `question_${questionCount}_choice_${newChoiceCount}`;
                        choiceInput.placeholder = await translate("Eine kurze und prägnante Antwortmöglichkeit", "de", userLang);

                        // Insert the "Answers:" label only when the second choice is added
                        if (newChoiceCount === 2) {
                            const answersLabel = document.createElement("label");
                            answersLabel.innerText = await translate("Antwortmöglichkeiten:", "de", userLang);
                            answersLabel.setAttribute("for", "answers");
                            choiceContainer.insertBefore(answersLabel, choiceContainer.children[2]);
                        }
                        choiceContainer.appendChild(choiceInput);
                    }

                        const choiceInput = document.createElement("input");
                        choiceInput.type = "text";
                        choiceInput.name = `question_${questionCount}_choice_1`;
                        choiceInput.placeholder = await translate("Eine kurze und prägnante Frage", "de", userLang);

                        if (questionType !== "dropdown") {
                            const choiceRadio = document.createElement("input");
                            choiceRadio.type = questionType === "single_choice" ? "radio" : "checkbox";
                            choiceRadio.name = `question_${questionCount}_choice_1_value`;
                            choiceContainer.appendChild(choiceRadio);
                        }

                        choiceContainer.appendChild(choiceInput);
                        questionWrapper.appendChild(choiceContainer);

                        const addButton = document.createElement("button");
                        addButton.innerText = await translate("Antwort hinzufügen", "de", userLang);
                        addButton.type = "button";
                        addButton.addEventListener("click", () => addChoice(choiceContainer));
                        questionWrapper.appendChild(addButton);

                        const removeButton = document.createElement("button");
                        removeButton.innerText = await translate("Antwort löschen", "de", userLang);
                        removeButton.type = "button";
                        removeButton.addEventListener("click", () => removeChoice(choiceContainer));
                        questionWrapper.appendChild(removeButton);
                        break;
                }

                questionWrapper.appendChild(followUpForm);

                const deleteButton = document.createElement("button");
                deleteButton.innerText = await translate("Element löschen", "de", userLang);
                deleteButton.type = "button";
                deleteButton.addEventListener("click", () => deleteQuestion(questionWrapper));
                questionWrapper.appendChild(deleteButton);

                const moveUpButton = document.createElement("button");
                moveUpButton.innerText = await translate("Element nach oben bewegen", "de", userLang);
                moveUpButton.type = "button";
                moveUpButton.addEventListener("click", () => moveQuestion(questionWrapper, "up"));
                questionWrapper.appendChild(moveUpButton);

                const moveDownButton = document.createElement("button");
                moveDownButton.innerText = await translate("Element nach unten bewegen", "de", userLang);
                moveDownButton.type = "button";
                moveDownButton.addEventListener("click", () => moveQuestion(questionWrapper, "down"));
                questionWrapper.appendChild(moveDownButton);

                questionsContainer.appendChild(questionWrapper);

                const command = new AddQuestionCommand(questionWrapper, questionsContainer);
                command.execute();
                undoStack.push(command);
                await preventUserLeave();
                clearRedoStack(); // Clear the redo stack
            });

            // Delete question function
            async function deleteQuestion(questionWrapper) {
                const command = new DeleteQuestionCommand(questionWrapper, questionsContainer);
                command.nextSibling = questionWrapper.nextElementSibling;
                command.execute();
                undoStack.push(command);
                await preventUserLeave();
                clearRedoStack(); // Clear the redo stack
            }

            // Move question function
            async function moveQuestion(questionWrapper, direction) {
                const command = new MoveQuestionCommand(questionWrapper, questionsContainer, direction);
                command.execute();
                undoStack.push(command);
                await preventUserLeave();
                clearRedoStack(); // Clear the redo stack
            }

            // Undo event listener
            undoBtn.addEventListener("click", async () => {
                if (undoStack.length > 0) {
                    const command = undoStack.pop();
                    command.unexecute();
                    redoStack.push(command);
                    await preventUserLeave();
                }
            });

            // Redo event listener
            redoBtn.addEventListener("click", async () => {
                if (redoStack.length > 0) {
                    const command = redoStack.pop();
                    command.execute();
                    undoStack.push(command);
                    await preventUserLeave();
                }
            });

            function removeChoice(choiceContainer) {
                const choiceCount = parseInt(choiceContainer.dataset.choiceCount, 10);
                if (choiceCount === 1) return;

                const lastChoiceInput = choiceContainer.lastElementChild;
                if (choiceContainer.tagName !== "SELECT") {
                    const lastChoiceRadio = choiceContainer.children[choiceContainer.children.length - 2];
                    choiceContainer.removeChild(lastChoiceRadio);
                }

                choiceContainer.removeChild(lastChoiceInput);
                choiceContainer.dataset.choiceCount = choiceCount - 1;

                // Remove the "Answers:" label when only one choice remains
                if (choiceCount === 2) {
                    const answersLabel = choiceContainer.querySelector('label[for="answers"]');
                    if (answersLabel) {
                        choiceContainer.removeChild(answersLabel);
                    }
                }
            }

            // Update question numbers function
            async function updateQuestionNumbers() {
                const questionWrappers = questionsContainer.querySelectorAll(".question-wrapper");
                for (const [index, wrapper] of questionWrappers.entries()) {
                    const questionLabel = wrapper.querySelector("label");
                    const questionElementType = questionLabel.className;
                    questionLabel.innerText = await translate("Element", "de", userLang) + `${index + 1}: ` + await typeToReadableType(questionElementType);

                    const inputs = wrapper.querySelectorAll("input, textarea, select");
                    inputs.forEach(input => {
                        const nameParts = input.name.split("_");
                        nameParts[1] = index + 1;
                        input.name = nameParts.join("_");
                    });
                }
            }
        } catch (error) {
            console.error("An error occurred:", error);
        }
    });

    //convert the machine readable types into human readable types
    async function typeToReadableType(value) {
        try {
            switch (value) {
                case "description":
                    return await translate("Beschreibender Text", "de", userLang);
                case "free_text":
                    return await translate("Freie Texteingabe", "de", userLang);
                case "picture":
                    return await translate("Bild", "de", userLang);
                case "single_choice":
                    return await translate("Frage mit Einfachauswahl (nebeneinander)", "de", userLang);
                case "multiple_choice":
                    return await translate("Frage mit Mehrfachauswahl (nebeneinander)", "de", userLang);
                case "dropdown":
                    return await translate("Frage mit Mehrfachauswahl (untereinander in einem Menü)", "de", userLang);
                default:
                    return "";
            }
        } catch (error) {
            console.error("An error occurred:", error);
        }
    }

    function addImageUploadEventListener(element) {
        if (element.hasAttribute("data-upload-listener")) {
            return; // Skip if the event listener is already attached
        }

        element.setAttribute("data-upload-listener", "true");
        element.addEventListener("change", function (event) {
            handleImageUpload(event, this.name);
        });
    }

    function collectData(isFinal) {
        let dataArray = [];

        dataArray[0] = [
            document.documentElement.getAttribute("lang") + (isFinal ? "_final" : "_draft"),
            document.getElementById("title").value,
            document.getElementById("subtitle").value,
            document.getElementById("description").value,
            document.getElementById("further_description").value,
            document.getElementById("contributors").value
        ];

        let targetGroup = document.getElementById("target_group").value;
        let emailDomain = document.getElementById("email_domain").value;

        dataArray[1] = [
            targetGroup === "other" ? emailDomain : targetGroup
        ];

        let questionWrappers = document.querySelectorAll(".question-wrapper");

        questionWrappers.forEach((wrapper, index) => {
            let questionType = wrapper.getAttribute("data-question-type");
            let checkBox = wrapper.querySelector("input[type='checkbox']");
            let isFollowUp = checkBox.checked ? "is_follow_up" : "";
            let inputs = wrapper.querySelectorAll("input[type='text']");
            let inputValues = Array.from(inputs).map(input => input.value);

            dataArray[index + 2] = [questionType, isFollowUp, ...inputValues];
        });
        const inputs = document.getElementsByTagName("input");
        for (let i = 0; i < inputs.length; i++) {
            if (inputs[i].type === "file" && inputs[i].name.match(/^question_\d+_picture$/)) {
                addImageUploadEventListener(inputs[i]);
            }
        }

        return dataArray;
    }

    function handleImageUpload(event, inputName) {
        const file = event.target.files[0];
        if (file.size > 30 * 1024 * 1024) {
            alert("File is too large (over 30MB). Please choose a smaller file.");
            return;
        }

        const formData = new FormData();
        formData.append("image", file, inputName + getFileExtension(file.name, true));

        const xhr = new XMLHttpRequest();
        xhr.open("POST", "assets/php/upload.php", true);
        xhr.onload = function () {
            if (this.status === 200) {
                if (testDomain) console.log("Image uploaded successfully.");
            } else {
                console.error("An error occurred during the image upload.");
            }
        };
        xhr.send(formData);
    }

    function getFileExtension(filename, withDot = false) {
        const extension = filename.slice((filename.lastIndexOf(".") - 1 >>> 0) + 2);
        return withDot ? '.' + extension : extension;
    }

    function sendDataToServer(dataArray) {
        const xhr = new XMLHttpRequest();
        xhr.open("POST", "assets/php/process_data.php", true);
        xhr.setRequestHeader("Content-Type", "application/json;charset=UTF-8");
        xhr.onload = function () {
            if (this.status === 200) {
                if (testDomain) console.log("Data sent successfully.");
            } else {
                console.error("An error occurred while sending data.");
            }
        };
        xhr.send(JSON.stringify(dataArray));
    }

    async function preventUserLeave() {
        if (undoStack.length > 0) {
            window.onbeforeunload = async function() {
                return await translate("Bist Du sicher, dass Du die Seite verlassen willst? Alle nicht gespeicherten Änderungen gehen dabei verloren.", "de", userLang);
            };
        } else {
            window.onbeforeunload = null;
        }
    }

</script>