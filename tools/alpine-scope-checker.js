/**
 * Alpine.js Scope Checker
 * 
 * Dieses Tool überprüft View-Dateien auf häufige Alpine.js Scope-Probleme:
 * 1. x-if/x-show außerhalb des zugehörigen x-data Scopes
 * 2. @click Handler, die auf Variablen zugreifen, die nicht im Scope sind
 * 3. Verschachtelte x-data Scopes, die zu Konflikten führen können
 */

const fs = require('fs');
const path = require('path');

class AlpineScopeChecker {
    constructor() {
        this.issues = [];
        this.warnings = [];
    }

    /**
     * Überprüft eine einzelne Datei
     */
    checkFile(filePath) {
        const content = fs.readFileSync(filePath, 'utf-8');
        const lines = content.split('\n');
        
        console.log(`\n🔍 Überprüfe: ${filePath}`);
        
        // Finde alle x-data Deklarationen mit ihren Variablen
        const xDataScopes = this.findXDataScopes(content);
        
        // Überprüfe x-if/x-show Direktiven
        this.checkXIfScopes(content, xDataScopes, filePath);
        
        // Überprüfe @click Handler
        this.checkClickHandlers(content, xDataScopes, filePath);
        
        // Überprüfe verschachtelte Scopes
        this.checkNestedScopes(content, filePath);
        
        // Überprüfe template x-if="true" (Anti-Pattern)
        this.checkTemplateXIfTrue(content, filePath);
    }

    /**
     * Findet alle x-data Scopes und ihre Variablen
     */
    findXDataScopes(content) {
        const scopes = [];
        const regex = /x-data="([^"]*)"/g;
        let match;
        
        while ((match = regex.exec(content)) !== null) {
            const dataString = match[1];
            const position = match.index;
            
            // Extrahiere Variablennamen aus dem x-data Objekt
            // z.B. { showModal: false, editId: null } => ['showModal', 'editId']
            const variables = this.extractVariables(dataString);
            
            scopes.push({
                dataString,
                position,
                variables,
                line: this.getLineNumber(content, position)
            });
        }
        
        return scopes;
    }

    /**
     * Extrahiert Variablennamen aus einem x-data String
     */
    extractVariables(dataString) {
        const variables = [];
        
        // Einfacher Parser für { var1: value1, var2: value2 }
        const objectRegex = /(\w+):/g;
        let match;
        
        while ((match = objectRegex.exec(dataString)) !== null) {
            variables.push(match[1]);
        }
        
        return variables;
    }

    /**
     * Überprüft x-if und x-show Direktiven
     */
    checkXIfScopes(content, xDataScopes, filePath) {
        const xIfRegex = /x-(if|show)="([^"]*)"/g;
        let match;
        
        while ((match = xIfRegex.exec(content)) !== null) {
            const directive = match[1]; // 'if' oder 'show'
            const expression = match[2]; // z.B. 'showModal'
            const position = match.index;
            const line = this.getLineNumber(content, position);
            
            // Finde zuständigen Scope
            const scope = this.findResponsibleScope(xDataScopes, position);
            
            if (!scope) {
                this.issues.push({
                    file: filePath,
                    line,
                    type: 'ERROR',
                    message: `x-${directive}="${expression}" außerhalb eines x-data Scopes!`
                });
                continue;
            }
            
            // Extrahiere Variable aus Expression (einfache Fälle)
            const usedVariables = this.extractUsedVariables(expression);
            
            for (const variable of usedVariables) {
                if (!scope.variables.includes(variable)) {
                    this.issues.push({
                        file: filePath,
                        line,
                        type: 'ERROR',
                        message: `x-${directive} verwendet '${variable}', aber diese Variable ist nicht im x-data Scope definiert!`
                    });
                }
            }
        }
    }

    /**
     * Überprüft @click Handler
     */
    checkClickHandlers(content, xDataScopes, filePath) {
        const clickRegex = /@click="([^"]*)"/g;
        let match;
        
        while ((match = clickRegex.exec(content)) !== null) {
            const handler = match[1]; // z.B. 'showModal = true'
            const position = match.index;
            const line = this.getLineNumber(content, position);
            
            // Finde zuständigen Scope
            const scope = this.findResponsibleScope(xDataScopes, position);
            
            if (!scope) {
                this.warnings.push({
                    file: filePath,
                    line,
                    type: 'WARNING',
                    message: `@click="${handler}" außerhalb eines x-data Scopes!`
                });
                continue;
            }
            
            // Extrahiere verwendete Variablen
            const usedVariables = this.extractUsedVariables(handler);
            
            for (const variable of usedVariables) {
                if (!scope.variables.includes(variable)) {
                    this.issues.push({
                        file: filePath,
                        line,
                        type: 'ERROR',
                        message: `@click verwendet '${variable}', aber diese Variable ist nicht im x-data Scope definiert!`
                    });
                }
            }
        }
    }

    /**
     * Überprüft verschachtelte Scopes
     */
    checkNestedScopes(content, filePath) {
        const scopes = this.findXDataScopes(content);
        
        for (let i = 0; i < scopes.length; i++) {
            for (let j = i + 1; j < scopes.length; j++) {
                if (scopes[j].position < scopes[i].position + 1000) {
                    this.warnings.push({
                        file: filePath,
                        line: scopes[j].line,
                        type: 'WARNING',
                        message: `Möglicher verschachtelter x-data Scope. Überprüfen Sie, ob die Scopes korrekt strukturiert sind.`
                    });
                }
            }
        }
    }

    /**
     * Überprüft auf <template x-if="true"> Anti-Pattern
     */
    checkTemplateXIfTrue(content, filePath) {
        const regex = /<template\s+x-if="true">/gi;
        let match;
        
        while ((match = regex.exec(content)) !== null) {
            const position = match.index;
            const line = this.getLineNumber(content, position);
            
            this.warnings.push({
                file: filePath,
                line,
                type: 'WARNING',
                message: `Anti-Pattern gefunden: <template x-if="true">. Dies sollte durch eine Variable ersetzt werden oder ganz entfernt werden.`
            });
        }
    }

    /**
     * Findet den zuständigen Scope für eine Position
     */
    findResponsibleScope(scopes, position) {
        // Finde den nächsten Scope vor der Position
        let closestScope = null;
        let closestDistance = Infinity;
        
        for (const scope of scopes) {
            if (scope.position < position) {
                const distance = position - scope.position;
                if (distance < closestDistance) {
                    closestDistance = distance;
                    closestScope = scope;
                }
            }
        }
        
        return closestScope;
    }

    /**
     * Extrahiert verwendete Variablen aus einem Ausdruck
     */
    extractUsedVariables(expression) {
        const variables = [];
        
        // Einfacher Parser für häufige Muster
        // showModal, showModal = true, editId = 5, etc.
        const patterns = [
            /(\w+)\s*=/g,  // variable = value
            /(\w+)\s*\(/g,  // function()
            /^(\w+)$/,      // nur variable
        ];
        
        for (const pattern of patterns) {
            let match;
            const regex = new RegExp(pattern);
            while ((match = regex.exec(expression)) !== null) {
                const variable = match[1];
                if (variable && !variables.includes(variable) && !this.isReservedWord(variable)) {
                    variables.push(variable);
                }
            }
        }
        
        return variables;
    }

    /**
     * Überprüft ob ein Wort ein reserviertes JavaScript Keyword ist
     */
    isReservedWord(word) {
        const reserved = ['true', 'false', 'null', 'undefined', 'window', 'document', 'console'];
        return reserved.includes(word);
    }

    /**
     * Ermittelt die Zeilennummer für eine Position
     */
    getLineNumber(content, position) {
        return content.substring(0, position).split('\n').length;
    }

    /**
     * Durchsucht ein Verzeichnis rekursiv
     */
    scanDirectory(directory, extension = '.php') {
        const files = fs.readdirSync(directory);
        
        for (const file of files) {
            const filePath = path.join(directory, file);
            const stat = fs.statSync(filePath);
            
            if (stat.isDirectory()) {
                this.scanDirectory(filePath, extension);
            } else if (filePath.endsWith(extension)) {
                this.checkFile(filePath);
            }
        }
    }

    /**
     * Gibt die Ergebnisse aus
     */
    printResults() {
        console.log('\n' + '='.repeat(80));
        console.log('📊 ERGEBNISSE');
        console.log('='.repeat(80));
        
        if (this.issues.length === 0 && this.warnings.length === 0) {
            console.log('\n✅ Keine Probleme gefunden! Alle Alpine.js Scopes sind korrekt.\n');
            return true;
        }
        
        if (this.issues.length > 0) {
            console.log('\n❌ FEHLER:');
            for (const issue of this.issues) {
                console.log(`  ${issue.file}:${issue.line}`);
                console.log(`    ${issue.message}\n`);
            }
        }
        
        if (this.warnings.length > 0) {
            console.log('\n⚠️  WARNUNGEN:');
            for (const warning of this.warnings) {
                console.log(`  ${warning.file}:${warning.line}`);
                console.log(`    ${warning.message}\n`);
            }
        }
        
        console.log('='.repeat(80));
        console.log(`Gesamt: ${this.issues.length} Fehler, ${this.warnings.length} Warnungen`);
        console.log('='.repeat(80) + '\n');
        
        return this.issues.length === 0;
    }
}

// Main
if (require.main === module) {
    const checker = new AlpineScopeChecker();
    const viewsDirectory = path.join(__dirname, '..', 'app', 'Views');
    
    console.log('🔍 Alpine.js Scope Checker');
    console.log('📁 Überprüfe Verzeichnis:', viewsDirectory);
    
    checker.scanDirectory(viewsDirectory);
    const success = checker.printResults();
    
    process.exit(success ? 0 : 1);
}

module.exports = AlpineScopeChecker;

