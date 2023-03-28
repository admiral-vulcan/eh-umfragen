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
        <option value="ehlb_students"><?php echo translate('Studierende der EH', 'de', $GLOBALS['lang']); ?></option>
        <option value="ehlb_lecturers"><?php echo translate('Dozierende und Mitarbeitende der EH', 'de', $GLOBALS['lang']); ?></option>
        <option value="ehlb_all"><?php echo translate('Alle an der EH', 'de', $GLOBALS['lang']); ?></option>
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

    function clearRedoStack() {
        redoStack = [];
        document.getElementById("button_redo").setAttribute("disabled", true);
    }

    function clearUndoStack() {
        undoStack = [];
        document.getElementById("button_undo").setAttribute("disabled", true);
    }

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

            class AddChoiceCommand {
                constructor(choiceContainer, choiceInput) {
                    this.choiceContainer = choiceContainer;
                    this.choiceInput = choiceInput;
                }

                async execute() {
                    this.choiceContainer.appendChild(this.choiceInput);
                    await preventUserLeave();
                }

                async unexecute() {
                    this.choiceContainer.removeChild(this.choiceInput);
                    await preventUserLeave();
                }
            }


            class RemoveChoiceCommand {
                constructor(choiceContainer, lastChoiceInput) {
                    this.choiceContainer = choiceContainer;
                    this.lastChoiceInput = lastChoiceInput;
                }

                execute() {
                    this.choiceContainer.removeChild(this.lastChoiceInput);
                }

                unexecute() {
                    this.choiceContainer.appendChild(this.lastChoiceInput);
                }
            }

            class ToggleFollowUpCheckboxCommand extends Command {
                constructor(followUpCheckbox) {
                    super();
                    this.followUpCheckbox = followUpCheckbox;
                }

                execute() {
                    this.followUpCheckbox.checked = !this.followUpCheckbox.checked;
                }

                unexecute() {
                    this.followUpCheckbox.checked = !this.followUpCheckbox.checked;
                }
            }

            class TextInputChangeCommand extends Command {
                constructor(inputElement, oldValue, newValue) {
                    super();
                    this.inputElement = inputElement;
                    this.oldValue = oldValue;
                    this.newValue = newValue;
                }

                execute() {
                    this.inputElement.value = this.newValue;
                }

                unexecute() {
                    this.inputElement.value = this.oldValue;
                }
            }

            class TextInputUndoHandler {
                constructor(inputElement) {
                    this.inputElement = inputElement;
                    this.textChangeTimeout = null;
                    this.cachedOldValue = undefined;
                    this.cachedNewValue = undefined;
                    this.attachListener();
                }

                attachListener() {
                    this.inputElement.addEventListener("input", (event) => {
                        const inputElement = event.target;

                        // Initialize the cache if it's empty
                        if (this.cachedOldValue === undefined) {
                            this.cachedOldValue = inputElement.getAttribute("data-prev-value") || "";
                        }

                        this.cachedNewValue = inputElement.value;

                        // Clear the existing timeout if any
                        clearTimeout(this.textChangeTimeout);

                        const pushTextChangeCommand = () => {
                            const command = new TextInputChangeCommand(inputElement, this.cachedOldValue, this.cachedNewValue);
                            undoStack.push(command);
                            clearRedoStack();

                            // Update the data-prev-value attribute with the current value
                            inputElement.setAttribute("data-prev-value", this.cachedNewValue);

                            // Reset the cache and timeout
                            this.cachedOldValue = undefined;
                            this.cachedNewValue = undefined;
                            this.textChangeTimeout = null;
                        };

                        // Check if the last character is a non-alphanumeric character
                        if (event.data && /\W/.test(event.data)) {
                            pushTextChangeCommand();
                        } else {
                            // Set a timeout to push the command if the user hasn't typed for 2 seconds
                            this.textChangeTimeout = setTimeout(pushTextChangeCommand, 500);
                        }
                    });
                }
            }

            class ValueInputUndoHandler {
                constructor(inputElement) {
                    this.inputElement = inputElement;
                    this.attachListener();
                }

                attachListener() {
                    this.inputElement.addEventListener("change", (event) => {
                        const inputElement = event.target;
                        const oldValue = inputElement.getAttribute("data-prev-value") || "";
                        const newValue = inputElement.value;

                        const pushValueChangeCommand = () => {
                            const command = new TextInputChangeCommand(inputElement, oldValue, newValue);
                            undoStack.push(command);
                            clearRedoStack();

                            // Update the data-prev-value attribute with the current value
                            inputElement.setAttribute("data-prev-value", newValue);
                        };

                        pushValueChangeCommand();
                    });
                }
            }

            async function addChoice(choiceContainer) {
                const choiceCount = parseInt(choiceContainer.dataset.choiceCount, 10);
                const newChoiceCount = choiceCount + 1;
                choiceContainer.dataset.choiceCount = newChoiceCount;

                const choiceInput = document.createElement("input");
                choiceInput.type = "text";
                choiceInput.name = `question_${questionCount}_choice_${newChoiceCount}`;
                choiceInput.placeholder = await translate("Eine kurze und prägnante Antwortmöglichkeit", "de", userLang);

                if (newChoiceCount === 2) {
                    const answersLabel = document.createElement("label");
                    answersLabel.innerText = await translate("Antwortmöglichkeiten:", "de", userLang);
                    answersLabel.setAttribute("for", "answers");
                    choiceContainer.insertBefore(answersLabel, choiceContainer.children[2]);
                }

                const command = new AddChoiceCommand(choiceContainer, choiceInput);
                command.execute();
                undoStack.push(command);
                await preventUserLeave();
                clearRedoStack();
                new TextInputUndoHandler(choiceInput);
            }

            async function removeChoice(choiceContainer) {
                const choiceCount = parseInt(choiceContainer.dataset.choiceCount, 10);
                if (choiceCount === 1) return;

                const lastChoiceInput = choiceContainer.lastElementChild;

                const command = new RemoveChoiceCommand(choiceContainer, lastChoiceInput);
                command.execute();
                undoStack.push(command);
                await preventUserLeave();
                clearRedoStack();

                choiceContainer.dataset.choiceCount = choiceCount - 1;

                // Remove the "Answers:" label when only one choice remains
                if (choiceCount === 2) {
                    const answersLabel = choiceContainer.querySelector('label[for="answers"]');
                    if (answersLabel) {
                        choiceContainer.removeChild(answersLabel);
                    }
                }

                // Update input names
                const inputs = choiceContainer.querySelectorAll('input[type="text"]');
                inputs.forEach((input, index) => {
                    const nameParts = input.name.split("_");
                    const questionIndex = nameParts[1];
                    input.name = `question_${questionIndex}_choice_${index + 1}`;
                });
            }

            // Show email domain input if 'other' is selected in the target group dropdown
            targetGroupSelect.addEventListener("change", () => {
                toggleTargetGroupSelect();
            });

            function toggleTargetGroupSelect() {
                if (targetGroupSelect.value === "other") {
                    emailDomainInput.style.display = "inline";
                    emailDomainLabel.style.display = "inline";
                } else {
                    emailDomainInput.style.display = "none";
                    emailDomainLabel.style.display = "none";
                }
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

                followUpCheckbox.addEventListener("change", async () => {
                    const command = new ToggleFollowUpCheckboxCommand(followUpCheckbox);
                    undoStack.push(command);
                    await preventUserLeave();
                    clearRedoStack();
                });

                switch (questionType) {
                    case "description":
                        const descriptionInput = document.createElement("input");
                        descriptionInput.type = "text";
                        descriptionInput.name = `question_${questionCount}_description`;
                        questionWrapper.appendChild(descriptionInput);
                        new TextInputUndoHandler(descriptionInput);
                        break;
                    case "free_text":
                        const freeTextInput = document.createElement("input");
                        freeTextInput.type = "text";
                        freeTextInput.name = `question_${questionCount}_free_text`;
                        questionWrapper.appendChild(freeTextInput);
                        new TextInputUndoHandler(freeTextInput);
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
                        new TextInputUndoHandler(choiceInput);
                        questionWrapper.appendChild(choiceContainer);

                        const addButton = document.createElement("button");
                        addButton.innerText = await translate("Antwort hinzufügen", "de", userLang);
                        addButton.type = "button";
                        addButton.addEventListener("click", async () => {
                            await addChoice(choiceContainer);
                        });

                        questionWrapper.appendChild(addButton);

                        const removeButton = document.createElement("button");
                        removeButton.innerText = await translate("Antwort löschen", "de", userLang);
                        removeButton.type = "button";
                        removeButton.addEventListener("click", async () => {
                            await removeChoice(choiceContainer);
                        });

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
                    toggleTargetGroupSelect();
                }

                if (checkUndo()) document.getElementById("button_undo").removeAttribute("disabled");
                else document.getElementById("button_undo").setAttribute("disabled", true);
                if (checkRedo()) document.getElementById("button_redo").removeAttribute("disabled");
                else document.getElementById("button_redo").setAttribute("disabled", true);
            });

            // Redo event listener
            redoBtn.addEventListener("click", async () => {
                if (redoStack.length > 0) {
                    const command = redoStack.pop();
                    command.execute();
                    undoStack.push(command);
                    await preventUserLeave();
                    toggleTargetGroupSelect();
                }
                if (checkUndo()) document.getElementById("button_undo").removeAttribute("disabled");
                else document.getElementById("button_undo").setAttribute("disabled", true);
                if (checkRedo()) document.getElementById("button_redo").removeAttribute("disabled");
                else document.getElementById("button_redo").setAttribute("disabled", true);
            });

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


            const title = document.getElementById("title");
            const subtitle = document.getElementById("subtitle");
            const description = document.getElementById("description");
            const further_description = document.getElementById("further_description");
            const contributors = document.getElementById("contributors");
            const target_group = document.getElementById("target_group");
            const email_domain = document.getElementById("email_domain");

            new TextInputUndoHandler(title);
            new TextInputUndoHandler(subtitle);
            new TextInputUndoHandler(description);
            new TextInputUndoHandler(further_description);
            new TextInputUndoHandler(contributors);
            new ValueInputUndoHandler(target_group);
            new TextInputUndoHandler(email_domain);

            // here we handle presets
            let presets = [];
            presets[0] = {
                buttonText: "Was ist Deine Lieblingsfarbe?",
                questionType: "multiple_choice",
                question: "Was ist Deine Lieblingsfarbe?",
                answers: ["Rot", "Grün", "Blau", "Lila"],
                followUp: false
            };

            presets[1] = {
                buttonText: "Tiere",
                questionType: "single_choice",
                question: "Was ist Dein Lieblingstier",
                answers: ["Hund", "Katze", "Maus"],
                followUp: false
            };
            //add more presets

            //translate presets:
            for (var i = 0; i < presets.length; i++) {
                presets[i]["buttonText"] = await translate(presets[i]["buttonText"], "de", userLang);
                presets[i]["question"] = await translate(presets[i]["question"], "de", userLang);
                for (var j = 0; j < presets[i]["answers"].length; j++) {
                    presets[i]["answers"][j] = await translate(presets[i]["answers"][j], "de", userLang);
                }
            }

            function waitForQuestionAdded() {
                return new Promise((resolve) => {
                    const observer = new MutationObserver(() => {
                        observer.disconnect();
                        resolve();
                    });

                    observer.observe(questionsContainer, { childList: true });
                });
            }

            function waitForChoiceAdded(choiceContainer) {
                return new Promise((resolve) => {
                    const observer = new MutationObserver(() => {
                        observer.disconnect();
                        resolve();
                    });

                    observer.observe(choiceContainer, { childList: true });
                });
            }

            async function applyPreset(preset) {
                //first, jump to where the preset gets added
                jump("add-question", -200);

                // Set the question type in the select element
                questionTypeSelect.value = preset.questionType;

                // Trigger a click event on the addQuestionBtn
                addQuestionBtn.click();

                // Wait for the question to be added
                await waitForQuestionAdded();

                // Get the last added question
                const questionWrapper = questionsContainer.lastElementChild;

                // Set the question text
                let questionInput = questionWrapper.querySelector("input[type='text']");
                setInputValueWithUndoRedo(questionInput, preset.question);

                // Add answer options
                const choiceContainer = questionWrapper.querySelector(".choice-container");
                const addButton = questionWrapper.querySelector("button");
                for (var i = 0; i < preset.answers.length; i++) {
                    const answer = preset.answers[i];
                    addButton.click();
                    await waitForChoiceAdded(choiceContainer);
                    const choiceInputs = questionWrapper.querySelectorAll("input[type='text']");
                    const choiceInput = choiceInputs[i + 1];
                    setInputValueWithUndoRedo(choiceInput, answer);
                }

                // Set the follow-up checkbox state
                    const followUpCheckbox = questionWrapper.querySelector("input[type='checkbox']");
                    if (followUpCheckbox.checked !== preset.followUp) {
                        followUpCheckbox.click();
                    }

                if (checkUndo()) document.getElementById("button_undo").removeAttribute("disabled");
                else document.getElementById("button_undo").setAttribute("disabled", true);
                if (checkRedo()) document.getElementById("button_redo").removeAttribute("disabled");
                else document.getElementById("button_redo").setAttribute("disabled", true);
            }
            await applyCsv();
            async function applyCsv() {
                if (typeof openDeconstructJson !== "undefined") {
                    const openDeconstructArray = JSON.parse(openDeconstructJson);
                    document.getElementById("button_save").removeAttribute("disabled");
                    document.getElementById("button_close").removeAttribute("disabled");
                    document.getElementById("button_delete").removeAttribute("disabled");
                    document.getElementById("button_draft").removeAttribute("disabled");
                    document.getElementById("button_final").removeAttribute("disabled");
                    document.getElementById("button_evaluate").removeAttribute("disabled");
                    document.getElementById("button_presets").removeAttribute("disabled");
                    await undoAll();
                    clearTexts();
                    resetSelects();
                    mySurveys.style.display = "none";
                    builder.style.display = "block";

                    /** TODO these 5 have to be implemented...
                     *
                    const openedSid = openDeconstructArray[0][0].split("_")[0];
                    const openedFilename = openDeconstructArray[0][0].split("_")[1];
                    const openedCreator = openDeconstructArray[0][0].split("_")[2];
                    const openedLang = openDeconstructArray[0][0].split("_")[3];
                    const openedFinal = openDeconstructArray[0][0].split("_")[4] === "final";
                    */

                    const openedTitle = openDeconstructArray[0][1];
                    const openedSubtitle = openDeconstructArray[0][2];
                    const openedDescription = openDeconstructArray[0][3];
                    const openedFurtherDescription = openDeconstructArray[0][4];
                    const openedContributors = openDeconstructArray[0][5];
                    const openedTargetGroup = openDeconstructArray[1][0];
                    let openedEmailDomain = "";
                    if(!["ehlb_students", "ehlb_lecturers", "ehlb_all", "no_restriction"].includes(openedTargetGroup)) openedEmailDomain = openedTargetGroup;
                    document.getElementById('title').value = openedTitle;
                    document.getElementById('subtitle').value = openedSubtitle;
                    document.getElementById('description').value = openedDescription;
                    document.getElementById('further_description').value = openedFurtherDescription;
                    document.getElementById('contributors').value = openedContributors;

                    if (openedEmailDomain === "") {
                        document.getElementById('target_group').value = openedTargetGroup;
                    } else {
                        document.getElementById('target_group').value = "other";
                        document.getElementById('email_domain').value = openedEmailDomain;
                        document.getElementById('email_domain_label').style.display = 'block';
                        document.getElementById('email_domain').style.display = 'block';
                    }
                    let toBeApplied = [];
                    for (let i = 2; i < openDeconstructArray.length; i++) {
                        toBeApplied.answers = [];
                        toBeApplied.questionType = openDeconstructArray[i][0];
                        toBeApplied.followUp = openDeconstructArray[i][1] === "is_follow_up";
                        toBeApplied.question = openDeconstructArray[i][2];
                        for (let j = 3; j < openDeconstructArray[i].length; j++) {
                            if (openDeconstructArray[i][j] !== "")
                            toBeApplied.answers[j-3] = openDeconstructArray[i][j];
                        }
                        await applyPreset(toBeApplied);
                    }
                }




                //if (typeof openDeconstructJson !== "undefined")  {
                //openDeconstructJson;
                //}
            }

            function setInputValueWithUndoRedo(inputElement, newValue) {
                const oldValue = inputElement.value;
                const command = new TextInputChangeCommand(inputElement, oldValue, newValue);
                command.execute();
                undoStack.push(command);
                clearRedoStack();
            }

                const presetsContainer = document.getElementById("preset-buttons");

                for (let i = 0; i < presets.length; i++) {
                    const presetButton = document.createElement("button");
                    presetButton.className = "button-preset";
                    presetButton.type = "button";
                    presetButton.id = `preset-button-${i}`;
                    presetButton.innerText = presets[i].buttonText;

                    presetButton.addEventListener("click", async () => {
                        await applyPreset(presets[i]);
                    });

                    presetsContainer.appendChild(presetButton);
                }
            setMenuItemsPosition();
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
            originalSid + "_" + originalFilename + "_" + userCID + "_" + document.documentElement.getAttribute("lang") + (isFinal ? "_final" : "_draft"),
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
        return new Promise((resolve, reject) => {
            const xhr = new XMLHttpRequest();
            xhr.open("POST", "assets/php/process_data.php", true);
            xhr.setRequestHeader("Content-Type", "application/json;charset=UTF-8");
            xhr.onload = function () {
                if (this.status === 200) {
                    if (testDomain) console.log("Data sent successfully.");
                    resolve(this.responseText);
                } else {
                    console.error("An error occurred while sending data.");
                    reject(new Error("An error occurred while sending data."));
                }
            };
            xhr.send(JSON.stringify(dataArray));
        });
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

    async function undo() {
        if (undoStack.length === 0) return;

        const command = undoStack.pop();
        await command.unexecute();
        redoStack.push(command);
        await preventUserLeave();

        if (checkUndo()) document.getElementById("button_undo").removeAttribute("disabled");
        else document.getElementById("button_undo").setAttribute("disabled", true);
        if (checkRedo()) document.getElementById("button_redo").removeAttribute("disabled");
        else document.getElementById("button_redo").setAttribute("disabled", true);
    }

    async function redo() {
        if (redoStack.length === 0) return;

        const command = redoStack.pop();
        await command.execute();
        undoStack.push(command);
        await preventUserLeave();

        if (checkUndo()) document.getElementById("button_undo").removeAttribute("disabled");
        else document.getElementById("button_undo").setAttribute("disabled", true);
        if (checkRedo()) document.getElementById("button_redo").removeAttribute("disabled");
        else document.getElementById("button_redo").setAttribute("disabled", true);
    }

    async function preventUserLeaveEvent() {
        await preventUserLeave();
    }

    // Listen for click events
    document.addEventListener("click", preventUserLeaveEvent);

    // Listen for touch events
    document.addEventListener("touchstart", preventUserLeaveEvent);

    // Listen for key press events
    document.addEventListener("keypress", preventUserLeaveEvent);

    //CTRL-Z CTRL-Y
    document.addEventListener("keydown", async (event) => {
        if (event.ctrlKey && event.key === "z") {
            event.preventDefault(); // Prevent the browser's default undo behavior
            await undo();
        } else if (event.ctrlKey && event.key === "y") {
            event.preventDefault(); // Prevent the browser's default redo behavior
            await redo();
        }
    });

    function checkUndo() {
        return undoStack.length > 0;
    }

    function checkRedo() {
        return redoStack.length > 0;
    }


</script>