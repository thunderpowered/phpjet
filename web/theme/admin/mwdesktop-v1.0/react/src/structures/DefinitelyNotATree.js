export class DefinitelyNotATree {
    constructor(structure) {
        this.root = {children: structure};
        this.serialized = false;
    }

    returnSelf() {
        return new DefinitelyNotATree(JSON.parse(JSON.stringify(this.root.children)))
    }

    serialize(returnContent = true) {
        if (!this.serialized) {
            this.root = JSON.stringify(this.root);
            this.serialized = true;
        }
        if (returnContent) {
            return this.returnContent();
        }
    }

    unserialize(returnContent = true) {
        if (this.serialized) {
            this.root = JSON.parse(this.root);
            this.serialized = false;
        }
        if (returnContent) {
            return this.returnContent();
        }
    }

    returnContent() {
        return this.root.children;
    }

    findByIndexArray(indexArray) {
        let currentNode = this.root;
        for (let i = 0; i < indexArray.length; i++) {
            if (typeof currentNode.children === 'undefined') {
                return undefined;
            }
            currentNode = currentNode.children[indexArray[i]];
        }
        return currentNode;
    }

    insertElementByIndexArray(indexArray, element, returnStructure = true) {
        let currentNode = this.findByIndexArray(indexArray);
        if (typeof currentNode === 'undefined' || typeof currentNode.children === 'undefined') {
            return false;
        }

        currentNode.children.push(element);
        // since we got 'link' to the original array, we needn't bother further
        if (returnStructure) {
            return this.returnSelf();
        } else {
            return true;
        }
    }

    deleteElementByIndexArray(indexArray, returnStructure = true) {
        let arraySliced = indexArray.slice(0, indexArray.length - 1);
        let currentNode = this.findByIndexArray(arraySliced);
        let result = false;
        if (typeof currentNode !== 'undefined' && typeof currentNode.children !== 'undefined') {
            currentNode.children.splice(indexArray[length - 1], 1);
            result = true;
        }

        if (returnStructure) {
            return this.returnSelf();
        } else {
            return result;
        }
    }

    iterateAllNodesBFS(callback) {
        let stack = [this.root], node = {};
        while (stack.length > 0) {
            node = stack.pop();
            // iterate through current node children
            if (Array.isArray(node.children)) {
                node.children.forEach(childNode => {
                    stack.push(childNode);
                });
            }

            callback(node);
        }
    }

    iterateAllNodesDFS(callback, root = this.root) {
        callback(root);
        root.children.forEach(node => {
            // if node has a child
            if (Array.isArray(node.children)) {
                this.iterateAllNodesDFS(callback, node);
            }
        });
    }

    findAllChildrenByLevel(level) {
        // todo
    }
}