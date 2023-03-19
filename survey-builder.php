<div id="builder">
    <!-- Title -->
    <label for="title">Title of the survey:</label>
    <input type="text" name="title" id="title">

    <!-- Description -->
    <label for="description">Description of the survey:</label>
    <input type="text"  name="description" id="description">

    <!-- Additional contributors -->
    <label for="contributors">Additional contributors (separate by commas):</label>
    <input type="text" name="contributors" id="contributors">

    <!-- Target group -->
    <label for="target_group">Intended target group:</label>
    <select name="target_group" id="target_group">
        <option value="students">Students</option>
        <option value="lecturers">Lecturers</option>
        <option value="no_restriction">No restriction</option>
        <option value="other">Other</option>
    </select>
    <input type="text" name="email_domain" id="email_domain" placeholder="Email domain" style="display: none;">

    <!-- Questions container -->
    <div id="questions-container"></div>

    <!-- Question type dropdown -->
    <label for="question_type">Add element:</label>
    <select name="question_type" id="question_type">
        <option value="">Select element type</option>
        <option value="description">Description text</option>
        <option value="free_text">Free text field</option>
        <option value="picture">Picture</option>
        <option value="multiple_choice">Multiple choice question</option>
        <option value="single_choice">Single choice question</option>
        <option value="dropdown">Dropdown question</option>
    </select>

    <!-- Add question button -->
    <button type="button" id="add-question">Add question</button>

</div>

<script type="application/javascript">
    document.addEventListener("DOMContentLoaded", () => {
        const questionTypeSelect = document.getElementById("question_type");
        const addQuestionBtn = document.getElementById("add-question");
        const questionsContainer = document.getElementById("questions-container");
        const targetGroupSelect = document.getElementById("target_group");
        const emailDomainInput = document.getElementById("email_domain");
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
            } else {
                emailDomainInput.style.display = "none";
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
            questionLabel.innerText = `Question ${questionCount}:`;
            questionWrapper.appendChild(questionLabel);

            const followUpCheckbox = document.createElement("input");
            followUpCheckbox.type = "checkbox";
            followUpCheckbox.name = `question_${questionCount}_follow_up`;
            followUpCheckbox.id = `question_${questionCount}_follow_up`;

            const followUpForm = document.createElement("form");
            followUpForm.className = "not-selectable";

            const followUpLabel = document.createElement("label");
            followUpLabel.htmlFor = `question_${questionCount}_follow_up`;
            followUpLabel.innerText = "Is follow-up question";

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

                    if (choiceContainer.tagName !== "SELECT") {
                        const choiceRadio = document.createElement("input");
                        choiceRadio.type = choiceContainer.parentElement.dataset.questionType === "single_choice" ? "radio" : "checkbox";
                        choiceRadio.name = `question_${questionCount}_choice_${newChoiceCount}_value`;
                        choiceContainer.appendChild(choiceRadio);
                    }

                    // Insert the "Answers:" label only when the second choice is added
                    if (newChoiceCount === 2) {
                        const answersLabel = document.createElement("label");
                        answersLabel.innerText = "Answers:";
                        answersLabel.setAttribute("for", "answers");
                        choiceContainer.insertBefore(answersLabel, choiceContainer.children[2]);
                    }


                    choiceContainer.appendChild(choiceInput);
                }



                    const choiceInput = document.createElement("input");
                    choiceInput.type = "text";
                    choiceInput.name = `question_${questionCount}_choice_1`;

                    if (questionType !== "dropdown") {
                        const choiceRadio = document.createElement("input");
                        choiceRadio.type = questionType === "single_choice" ? "radio" : "checkbox";
                        choiceRadio.name = `question_${questionCount}_choice_1_value`;
                        choiceContainer.appendChild(choiceRadio);
                    }

                    choiceContainer.appendChild(choiceInput);
                    questionWrapper.appendChild(choiceContainer);

                    const addButton = document.createElement("button");
                    addButton.innerText = "Add answer";
                    addButton.type = "button";
                    addButton.addEventListener("click", () => addChoice(choiceContainer));
                    questionWrapper.appendChild(addButton);

                    const removeButton = document.createElement("button");
                    removeButton.innerText = "Delete answer";
                    removeButton.type = "button";
                    removeButton.addEventListener("click", () => removeChoice(choiceContainer));
                    questionWrapper.appendChild(removeButton);
                    break;
            }

            questionWrapper.appendChild(followUpForm);

            const deleteButton = document.createElement("button");
            deleteButton.innerText = "Delete";
            deleteButton.type = "button";
            deleteButton.addEventListener("click", () => deleteQuestion(questionWrapper));
            questionWrapper.appendChild(deleteButton);

            const moveUpButton = document.createElement("button");
            moveUpButton.innerText = "Up";
            moveUpButton.type = "button";
            moveUpButton.addEventListener("click", () => moveQuestion(questionWrapper, "up"));
            questionWrapper.appendChild(moveUpButton);

            const moveDownButton = document.createElement("button");
            moveDownButton.innerText = "Down";
            moveDownButton.type = "button";
            moveDownButton.addEventListener("click", () => moveQuestion(questionWrapper, "down"));
            questionWrapper.appendChild(moveDownButton);

            const addAfterButton = document.createElement("button");
            addAfterButton.innerText = "Add";
            addAfterButton.type = "button";
            addAfterButton.addEventListener("click", () => addQuestionAfter(questionWrapper));
            questionWrapper.appendChild(addAfterButton);

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


        function deleteQuestion(questionWrapper) {
            questionsContainer.removeChild(questionWrapper);
        }

        // Move question function
        function moveQuestion(questionWrapper, direction) {
            const command = new MoveQuestionCommand(questionWrapper, questionsContainer, direction);
            command.execute();
            updateQuestionNumbers();
            undoStack.push(command);
        }

        function addQuestionAfter(questionWrapper) {
            questionTypeSelect.value = "";
            addQuestionBtn.click();
            const newQuestionWrapper = questionsContainer.lastElementChild;
            questionsContainer.insertBefore(newQuestionWrapper, questionWrapper.nextElementSibling);
        }

// Update question numbers function
        function updateQuestionNumbers() {
            const questionWrappers = questionsContainer.querySelectorAll(".question-wrapper");
            questionWrappers.forEach((wrapper, index) => {
                const questionLabel = wrapper.querySelector("label");
                questionLabel.innerText = `Question ${index + 1}:`;

                const inputs = wrapper.querySelectorAll("input, textarea, select");
                inputs.forEach(input => {
                    const nameParts = input.name.split("_");
                    nameParts[1] = index + 1;
                    input.name = nameParts.join("_");
                });
            });
        }

    });
</script>