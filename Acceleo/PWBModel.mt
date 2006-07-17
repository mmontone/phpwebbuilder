<%
metamodel http://www.eclipse.org/uml2/1.0.0/UML
%>

<%script type="Class" name="genDesc" file="<%name%>.php"%>
class <%name%> extends PersistentObject{
/*
	Comment : <%ownedComment.body%>
*/
	function initialize(){
	<%for (attribute){%>
		<%if (type.name=="String") {%>
		$this->addField(new <%type.name%>Field(<%name%>,TRUE));
		<%}else{%>
		$this->addField(new IndexField(<%name%>,FALSE,<%type.name%>));
		<%}%>
	<%}%>
	//<%startUserCode%> methods
	//<%endUserCode%>
	}
}