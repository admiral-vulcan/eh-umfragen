<div id="builder">
    <!-- Title -->
    <label for="title"><?php echo translate('Umfragetitel', 'de', $GLOBALS['lang']); ?></label>
    <input type="text" name="title" id="title" placeholder="<?php echo translate('Ein prägnanter Titel, möglichst nur ein bis drei Wörter', 'de', $GLOBALS['lang']); ?>">

    <!-- Description -->
    <label for="description"><?php echo translate('Umfragebeschreibung', 'de', $GLOBALS['lang']); ?></label>
    <input type="text"  name="description" id="description" placeholder="<?php echo translate('Eine Beschreibung, die Sinn und Zweck der Umfrage erklärt', 'de', $GLOBALS['lang']); ?>">

    <!-- Additional contributors -->
    <label for="contributors"><?php echo translate('Weitere Mitwirkende (durch Kommata getrennt)', 'de', $GLOBALS['lang']); ?></label>
    <input type="text" name="contributors" id="contributors" placeholder="<?php echo translate('Z.B. musterfrau1@studnet.eh-ludwigsburg.de, mustermann@studnet.eh-ludwigsburg.de', 'de', $GLOBALS['lang']); ?>">

    <!-- Target group -->
    <label for="target_group"><?php echo translate('Anvisierte Zielgruppe', 'de', $GLOBALS['lang']); ?></label>
    <select name="target_group" id="target_group">
        <option value="students"><?php echo translate('Studierende der EH', 'de', $GLOBALS['lang']); ?></option>
        <option value="lecturers"><?php echo translate('Dozierende und Mitarbeitende der EH', 'de', $GLOBALS['lang']); ?></option>
        <option value="no_restriction"><?php echo translate('ohne Einschränkung', 'de', $GLOBALS['lang']); ?></option>
        <option value="other"><?php echo translate('Andere Zielgruppe', 'de', $GLOBALS['lang']); ?></option>
    </select>
    <label for="email_domain" id="email_domain_label" style="display: none;"><?php echo translate('E-Mail-Domains (durch Kommata getrennt)', 'de', $GLOBALS['lang']); ?></label><input type="text" name="email_domain" id="email_domain" placeholder="<?php echo translate('Z.B.', 'de', $GLOBALS['lang']); ?> @ph-ludwigsburg.de, @uni-stuttgart.de" style="display: none;">

    <!-- Questions container -->
    <div id="questions-container"></div>

    <!-- Question type dropdown -->
    <label for="question_type"><?php echo translate('Neues Element:', 'de', $GLOBALS['lang']); ?></label>
    <select name="question_type" id="question_type">
        <option value=""><?php echo translate('Elementtyp', 'de', $GLOBALS['lang']); ?></option>
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

<script type="application/javascript">
    document.addEventListener("DOMContentLoaded", () => {
        const questionTypeSelect = document.getElementById("question_type");
        const addQuestionBtn = document.getElementById("add-question");
        const questionsContainer = document.getElementById("questions-container");
        const targetGroupSelect = document.getElementById("target_group");
        const emailDomainInput = document.getElementById("email_domain");
        const emailDomainLabel = document.getElementById("email_domain_label");
        const undoBtn = document.getElementById("button_undo");
        const redoBtn = document.getElementById("button_redo");

        let questionCount = 0;
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
            }

            execute() {
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

        // Undo/redo stacks
        let undoStack = [];
        let redoStack = [];

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

        // Add question based on question type
        addQuestionBtn.addEventListener("click", () => {
            const questionType = questionTypeSelect.value;
            if (!questionType) return;

            questionCount++;

            const questionWrapper = document.createElement("div");
            questionWrapper.className = "question-wrapper";
            questionWrapper.dataset.questionType = questionType;

            const questionLabel = document.createElement("label");
            questionLabel.innerText = `<?php echo translate('Element', 'de', $GLOBALS['lang']); ?> ${questionCount}: ` + typeToReadableType(questionType);
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
            followUpLabel.innerText = "<?php echo translate('ist ein Follow-up-Element', 'de', $GLOBALS['lang']); ?>";

            followUpForm.appendChild(followUpCheckbox);
            followUpForm.appendChild(followUpLabel);


            switch (questionType) {
                case "description":
                    const descriptionInput = document.createElement("textarea");
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

                function addChoice(choiceContainer) {
                    const choiceCount = parseInt(choiceContainer.dataset.choiceCount, 10);
                    const newChoiceCount = choiceCount + 1;
                    choiceContainer.dataset.choiceCount = newChoiceCount;

                    const choiceInput = document.createElement("input");
                    choiceInput.type = "text";
                    choiceInput.name = `question_${questionCount}_choice_${newChoiceCount}`;
                    choiceInput.placeholder = "<?php echo translate('Eine kurze und prägnante Antwortmöglichkeit', 'de', $GLOBALS['lang']); ?>";

                    if (choiceContainer.tagName !== "SELECT") {
                        const choiceRadio = document.createElement("input");
                        choiceRadio.type = choiceContainer.parentElement.dataset.questionType === "single_choice" ? "radio" : "checkbox";
                        choiceRadio.name = `question_${questionCount}_choice_${newChoiceCount}_value`;
                        choiceContainer.appendChild(choiceRadio);
                    }

                    // Insert the "Answers:" label only when the second choice is added
                    if (newChoiceCount === 2) {
                        const answersLabel = document.createElement("label");
                        answersLabel.innerText = "<?php echo translate('Antwortmöglichkeiten:', 'de', $GLOBALS['lang']); ?>";
                        answersLabel.setAttribute("for", "answers");
                        choiceContainer.insertBefore(answersLabel, choiceContainer.children[2]);
                    }


                    choiceContainer.appendChild(choiceInput);
                }



                    const choiceInput = document.createElement("input");
                    choiceInput.type = "text";
                    choiceInput.name = `question_${questionCount}_choice_1`;
                    choiceInput.placeholder = "<?php echo translate('Eine kurze und prägnante Frage', 'de', $GLOBALS['lang']); ?>";

                    if (questionType !== "dropdown") {
                        const choiceRadio = document.createElement("input");
                        choiceRadio.type = questionType === "single_choice" ? "radio" : "checkbox";
                        choiceRadio.name = `question_${questionCount}_choice_1_value`;
                        choiceContainer.appendChild(choiceRadio);
                    }

                    choiceContainer.appendChild(choiceInput);
                    questionWrapper.appendChild(choiceContainer);

                    const addButton = document.createElement("button");
                    addButton.innerText = "<?php echo translate('Antwort hinzufügen', 'de', $GLOBALS['lang']); ?>";
                    addButton.type = "button";
                    addButton.addEventListener("click", () => addChoice(choiceContainer));
                    questionWrapper.appendChild(addButton);

                    const removeButton = document.createElement("button");
                    removeButton.innerText = "<?php echo translate('Antwort löschen', 'de', $GLOBALS['lang']); ?>";
                    removeButton.type = "button";
                    removeButton.addEventListener("click", () => removeChoice(choiceContainer));
                    questionWrapper.appendChild(removeButton);
                    break;
            }

            questionWrapper.appendChild(followUpForm);

            const deleteButton = document.createElement("button");
            deleteButton.innerText = "<?php echo translate('Element löschen', 'de', $GLOBALS['lang']); ?>";
            deleteButton.type = "button";
            deleteButton.addEventListener("click", () => deleteQuestion(questionWrapper));
            questionWrapper.appendChild(deleteButton);

            const moveUpButton = document.createElement("button");
            moveUpButton.innerText = "<?php echo translate('Element nach oben bewegen', 'de', $GLOBALS['lang']); ?>";
            moveUpButton.type = "button";
            moveUpButton.addEventListener("click", () => moveQuestion(questionWrapper, "up"));
            questionWrapper.appendChild(moveUpButton);

            const moveDownButton = document.createElement("button");
            moveDownButton.innerText = "<?php echo translate('Element nach unten bewegen', 'de', $GLOBALS['lang']); ?>";
            moveDownButton.type = "button";
            moveDownButton.addEventListener("click", () => moveQuestion(questionWrapper, "down"));
            questionWrapper.appendChild(moveDownButton);

            questionsContainer.appendChild(questionWrapper);

            const command = new AddQuestionCommand(questionWrapper, questionsContainer);
            command.execute();
            undoStack.push(command);
        });

        // Delete question function
        function deleteQuestion(questionWrapper) {
            const command = new DeleteQuestionCommand(questionWrapper, questionsContainer);
            command.nextSibling = questionWrapper.nextElementSibling;
            command.execute();
            undoStack.push(command);
        }

        // Move question function
        function moveQuestion(questionWrapper, direction) {
            const command = new MoveQuestionCommand(questionWrapper, questionsContainer, direction);
            command.execute();
            undoStack.push(command);
        }

        // Undo event listener
        undoBtn.addEventListener("click", () => {
            if (undoStack.length > 0) {
                const command = undoStack.pop();
                command.unexecute();
                redoStack.push(command);
            }
        });

        // Redo event listener
        redoBtn.addEventListener("click", () => {
            if (redoStack.length > 0) {
                const command = redoStack.pop();
                command.execute();
                undoStack.push(command);
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
        function updateQuestionNumbers() {
            const questionWrappers = questionsContainer.querySelectorAll(".question-wrapper");
            questionWrappers.forEach((wrapper, index) => {
                const questionLabel = wrapper.querySelector("label");
                const questionElementType = questionLabel.className;
                questionLabel.innerText = `<?php echo translate('Element', 'de', $GLOBALS['lang']); ?> ${index + 1}: ` + typeToReadableType(questionElementType);

                const inputs = wrapper.querySelectorAll("input, textarea, select");
                inputs.forEach(input => {
                    const nameParts = input.name.split("_");
                    nameParts[1] = index + 1;
                    input.name = nameParts.join("_");
                });
            });
        }

    });

    //convert the machine readable types into human readable types
    function typeToReadableType(value) {
        switch (value) {
            case "description":
                return "<?php echo translate('Beschreibender Text', 'de', $GLOBALS['lang']); ?>";
            case "free_text":
                return "<?php echo translate('Freie Texteingabe', 'de', $GLOBALS['lang']); ?>";
            case "picture":
                return "<?php echo translate('Bild', 'de', $GLOBALS['lang']); ?>";
            case "single_choice":
                return "<?php echo translate('Frage mit Einfachauswahl (nebeneinander)', 'de', $GLOBALS['lang']); ?>";
            case "multiple_choice":
                return "<?php echo translate('Frage mit Mehrfachauswahl (nebeneinander)', 'de', $GLOBALS['lang']); ?>";
            case "dropdown":
                return "<?php echo translate('Frage mit Mehrfachauswahl (untereinander in einem Menü)', 'de', $GLOBALS['lang']); ?>";
            default:
                return "";
        }
    }
</script>